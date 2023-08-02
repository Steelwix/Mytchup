<?php

namespace App\Controller;

use App\Entity\Champion;
use App\Form\DataManagerFormType;
use App\Service\API\GetAllChampsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class DataManagerController extends AbstractController
{
    private $httpClient;
    private $em;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em)
    {
        $this->httpClient = $httpClient;
        $this->em = $em;
    }

    #[Route('/data', name: 'app_data_manager')]
    public function index(Request $request): Response
    {
        $champions = $this->em->getRepository(Champion::class)->findAll();
        /** @var User $user */
        $user = $this->getUser();
        $picks = $user->getPicks();
        $datas = [];
        foreach ($picks as $pick){
        $matchups = $pick->getMatchups();
        $pickName = $pick->getChampion()->getName();
        foreach ($matchups as $matchup){
            $championName = $matchup->getOpponent()->getName();
            $datas[$pickName][$championName]['wonGames'] = $matchup->getWonGames();
            $datas[$pickName][$championName]['wonLanes'] = $matchup->getWonLanes();
            $datas[$pickName][$championName]['totalGames'] = $matchup->getTotalGames();
            $datas[$pickName][$championName]['totalLanes'] = $matchup->getTotalLanes();
        }

        foreach ($champions as $champion){
            if( !isset($datas[$pickName][$champion->getName()]['wonGames'])){
                $datas[$pickName][$champion->getName()]['wonGames'] = 0;
                $datas[$pickName][$champion->getName()]['totalGames'] = 0;
                $datas[$pickName][$champion->getName()]['wonLanes'] = 0;
                $datas[$pickName][$champion->getName()]['totalLanes'] = 0;
            }
        }}
        usort($champions, function($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });
        return $this->render('data_manager/index.html.twig', ['picks' => $picks, 'champions' => $champions, 'datas' => $datas]);
    }


    #[Route('/data/getAllChamps', name: 'get_all_champs_from_api')]
    public function getApiAllChamps(): void
    {
        $version = '13.7.1';
        $language = 'en_US';
        $apiKey = 'RGAPI-188c0b8b-fe79-4505-b039-107e42c931ec';

        $url = sprintf('https://ddragon.leagueoflegends.com/cdn/%s/data/%s/champion.json', $version, $language);

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'X-Riot-Token' => $apiKey,
            ],
        ]);

        $data = json_decode($response->getContent(), true);

        $championRepository = $this->em->getRepository(Champion::class);
        $storedChamps = $championRepository->findAll();
        $champInDatabase = array();
        foreach ($storedChamps as $stored) {
            $champInDatabase[] = $stored->getName();
        }
        $champNames = array_map(function ($champ) {
            return $champ['name'];
        }, $data['data']);

        $missingChamps = array_diff($champNames, $champInDatabase);

        if (count($missingChamps) !== 0) {
            foreach ($missingChamps as $missing) {
                $newChamp = new Champion;
                $newChamp->setName($missing);
                $this->em->persist($newChamp);
                $this->em->flush();
            }
        }
    }
}
