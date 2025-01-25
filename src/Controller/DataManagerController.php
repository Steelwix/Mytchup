<?php

    namespace App\Controller;

    use App\Entity\Champion;
    use App\Entity\Matchup;
    use App\Entity\Pick;
    use App\Form\DataManagerFormType;
    use App\Form\ImportStatFromSpreadSheetFormType;
    use App\Service\API\GetAllChampsService;
    use App\Service\DataService;
    use Doctrine\ORM\EntityManagerInterface;
    use PhpOffice\PhpSpreadsheet\Cell\Cell;
    use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\File\Exception\FileException;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Contracts\HttpClient\HttpClientInterface;


    class DataManagerController extends AbstractController
    {

        public function __construct(private HttpClientInterface $httpClient, private EntityManagerInterface $em, private DataService $dataService)
        {
            $this->httpClient = $httpClient;
            $this->em = $em;
            $this->dataService = $dataService;
        }

        #[Route('/data/list', name: 'app_data_manager')]
        public function index(Request $request): Response
        {
            $champions = $this->em->getRepository(Champion::class)->findAll();
            /** @var User $user */
            $user = $this->getUser();
            $picks = $user->getPicks();
            $datas = [];
            foreach ($picks as $pick) {
                $matchups = $pick->getMatchups();
                $pickName = $pick->getChampion()->getName();
                foreach ($matchups as $matchup) {
                    $championName = $matchup->getOpponent()->getName();
                    $datas[$pickName][$championName]['wonGames'] = $matchup->getWonGames();
                    $datas[$pickName][$championName]['wonLanes'] = $matchup->getWonLanes();
                    $datas[$pickName][$championName]['totalGames'] = $matchup->getTotalGames();
                    $datas[$pickName][$championName]['totalLanes'] = $matchup->getTotalLanes();
                }

                foreach ($champions as $champion) {
                    if (!isset($datas[$pickName][$champion->getName()]['wonGames'])) {
                        $datas[$pickName][$champion->getName()]['wonGames'] = 0;
                        $datas[$pickName][$champion->getName()]['totalGames'] = 0;
                        $datas[$pickName][$champion->getName()]['wonLanes'] = 0;
                        $datas[$pickName][$champion->getName()]['totalLanes'] = 0;
                    }
                }
            }
            usort($champions, function ($a, $b) {
                return strcmp($a->getName(), $b->getName());
            });
            return $this->render('data_manager/index.html.twig', ['picks' => $picks, 'champions' => $champions, 'datas' => $datas]);
        }

        #[Route('/data/import', name: 'app_data__import')]
        public function importDataFromExcel(Request $request): Response
        {
            ini_set('memory_limit', '512M'); // Or a higher value, like '512M'

            $form = $this->createForm(ImportStatFromSpreadSheetFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $file = $form->get('file')->getData();
                $uploadDir = $this->getParameter('kernel.project_dir') . '/var/uploads/';

                try {
                    $filePath = $uploadDir . $file->getClientOriginalName();
                    $file->move($uploadDir, $file->getClientOriginalName());

                    $reader = new Xlsx();
                    $spreadsheet = $reader->load($filePath);

                    $data = $spreadsheet->getActiveSheet()->toArray();
                    $allSheets = $spreadsheet->getAllSheets();

                    foreach ($allSheets as $key => $sheet) {
//                    if($key != 0 ){
//                        continue;
//                    }
                        $line = null;
                        $col = null;
                        $stats = null;
                        foreach ($sheet->getRowIterator() as $row) {
                            foreach ($row->getCellIterator() as $cell) {
                                $formula = $cell->getValue(Cell::class);
                                if ($formula == "" || $formula == null || $formula == " " || empty($formula) ) {
                                    continue;
                                }
                                    $coordinate = $cell->getCoordinate();
                                    preg_match('/([A-Z]+)(\d+)/', $coordinate, $matches);
                                    $coordLetter = $matches[1];
                                    $coordNumber = $matches[2];
                                    if ($coordNumber == 1) {
                                        $line[$coordLetter] = $formula;
                                        continue;
                                    }
                                    if ($coordLetter == 'A') {
                                        $col[$coordNumber] = $formula;
                                        continue;
                                    }
                                    $stats[$coordinate]['pick'] = $line[$coordLetter];
                                    $stats[$coordinate]['enemy'] = $col[$coordNumber];
                                    $formula = ltrim($formula, '=');

                                    $fractions = explode('+', $formula);
                                    $gamesFraction = $fractions[0];         // First fraction always exists
                                    $lanesFraction = $fractions[1]??null; // Second fraction might not exist
                                if (!str_contains($gamesFraction, '/')) {
                                    $gamesFraction = "0/0";
                                }
                                    list($wonGames, $totalGames) = explode('/', $gamesFraction);
                                    $stats[$coordinate]['wonGames'] = $wonGames;
                                    $stats[$coordinate]['totalGames'] = $totalGames;
                                    if ($lanesFraction != null) {
                                        if (!str_contains($lanesFraction, '/')) {
                                            $lanesFraction = "0/0";

                                        }
                                        list($wonLanes, $totalLanes) = explode('/', $lanesFraction);
                                        $stats[$coordinate]['wonLanes'] = $wonLanes;
                                        $stats[$coordinate]['totalLanes'] = $totalLanes;
                                    }

                            }
                        }
                        foreach ($stats as $position => $stat) {
                            $champion = $this->em->getRepository(Champion::class)->findOneBy(['name' => $stat['pick']]);
                            if(!$champion){
                                dump($stat['pick']);
                            }
                            $opponent = $this->em->getRepository(Champion::class)->findOneBy(['name' => $stat['enemy']]);
                            if(!$opponent){
                                dump($stat['enemy']);
                            }
                            $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($this->getUser(), $champion);
                            if (!$pick) {
                                $pick = new Pick();
                                $pick->setChampion($champion);
                                $pick->setPlayer($this->getUser());
                                $this->em->persist($pick);
                            }
                            $matchup = $this->em->getRepository(Matchup::class)->findOneBy(['pick' => $pick, 'opponent' => $opponent]);
                            if (!$matchup) {
                                $matchup = new Matchup();
                                $matchup->setPick($pick);
                                $matchup->setOpponent($opponent);
                                $matchup->setWonGames((int)$stat['wonGames']);
                                $matchup->setTotalGames((int)$stat['totalGames']);
                                if (isset($stat['wonLanes']) && isset($stat['totalLanes'])) {
                                    $matchup->setWonLanes($stat['wonLanes']);
                                    $matchup->setTotalLanes($stat['totalLanes']);
                                }

                                $this->em->persist($matchup);
                                $this->em->flush();
                                continue;
                            }
                            $matchup->setWonGames($matchup->getWonGames() + (int)$stat['wonGames']);
                            $matchup->setTotalGames($matchup->getTotalGames() + (int)$stat['totalGames']);
                            if (isset($stat['wonLanes']) && isset($stat['totalLanes'])) {
                                $matchup->setWonLanes($matchup->getWonLanes() + $stat['wonLanes']);
                                $matchup->setTotalLanes($matchup->getTotalLanes() + $stat['totalLanes']);
                            }
                            $this->em->flush();

                        }
                    }
                    return $this->json(['success' => true, 'data' => $data]);
                } catch (FileException $e) {
                    return $this->json(['success' => false, 'error' => $e->getMessage()]);
                }
            }

            return $this->render('data_manager/import.html.twig', ['form' => $form->createView()]);
        }


        #[Route('/data/getAllChamps', name: 'get_all_champs_from_api')]
        public function getApiAllChamps(): void
        {
            //TODO: auto get version
            $version = '14.24.1';
            $language = 'en_US';
            $apiKey = 'RGAPI-188c0b8b-fe79-4505-b039-107e42c931ec';

            $url = sprintf('https://ddragon.leagueoflegends.com/cdn/%s/data/%s/champion.json', $version, $language);

            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'X-Riot-Token' => $apiKey,
                ],
            ]);

            $data = json_decode($response->getContent(), true)['data'] ?? [];
            $championNamesFromApi = array_map(fn($champ) => $champ['name'], $data);

            $championRepository = $this->em->getRepository(Champion::class);
            $storedChampNames = $championRepository->createQueryBuilder('c')
                ->select('c.name')
                ->getQuery()
                ->getArrayResult();
            $storedChampNames = array_column($storedChampNames, 'name');

            $missingChamps = array_diff($championNamesFromApi, $storedChampNames);

            if (!empty($missingChamps)) {
                foreach ($missingChamps as $missing) {
                    $newChamp = (new Champion())->setName($missing);
                    $this->em->persist($newChamp);
                }
                $this->em->flush();
            }
        }

    }
