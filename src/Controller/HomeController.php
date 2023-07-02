<?php

namespace App\Controller;

use App\Entity\Champion;
use App\Form\PushNewStatFormType;
use App\Service\API\GetAllChampsService;
use App\Service\MatchupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{

    private $allChampsService;
    private $em;
    private $matchupManager;
    public function __construct(GetAllChampsService $allChampsService, EntityManagerInterface $em,
    MatchupService $matchupManager)
    {
        $this->allChampsService = $allChampsService;
        $this->em = $em;
        $this->matchupManager = $matchupManager;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $championRepository = $this->em->getRepository(Champion::class);
        $storedChamps = $championRepository->findAll();
        $globalWonGames = $globalWonLanes = $globalTotalGames = $globalTotalLanes = 0;
        $form = $this->createForm(PushNewStatFormType::class);
        if ($this->getUser() != null) {


            /** @var User $user */
            $user = $this->getUser();
            $picks = $user->getPicks();

            $bestMatchups = [];
            $worstMatchups = [];

            foreach ($picks as $pick) {
                $matchups = $pick->getMatchups();
                foreach ($matchups as $matchup) {

                    $winRate = ($matchup->getWonGames() / $matchup->getTotalGames()) * 100;
                    if(count($bestMatchups) < 6){
                        $bestMatchups[] = $this->matchupManager->defineBestMatchups($matchup, $winRate, $pick);
                    }
                    else {

                        foreach ($bestMatchups as  $key => $best){
                            if($best['win_rate'] < $winRate){
                                break;
                            }
                        }
                        $bestMatchups[$key] = $this->matchupManager->defineBestMatchups($matchup, $winRate, $pick);
                    }
                    $globalWonGames = $globalWonGames + $matchup->getWonGames();
                    $globalWonLanes = $globalWonLanes + $matchup->getWonLanes();
                    $globalTotalGames = $globalTotalGames + $matchup->getTotalGames();
                    $globalTotalLanes = $globalTotalLanes + $matchup->getTotalLanes();
                }
            }
        }
        $globalWinRate = $globalLaneWinRate = $globalOverallRate = 0;
        if ($globalTotalGames != 0) {
            $globalWinRate = ($globalWonGames / $globalTotalGames) * 100;
        }
        if ($globalTotalLanes != 0) {
            $globalLaneWinRate = ($globalWonLanes / $globalTotalLanes) * 100;
        }
        $globalOverallRate = ($globalWinRate + $globalLaneWinRate) / 2;
        return $this->render('home/index.html.twig', [
            'newGameStat' => $form->createView(), 'globalWonGames' => $globalWonGames, 'globalWonLanes' => $globalWonLanes,
            'globalTotalGames' => $globalTotalGames, 'globalTotalLanes' => $globalTotalLanes, 'globalWinRate' => $globalWinRate,
            'globalLaneWinRate' => $globalLaneWinRate, 'globalOverallRate' => $globalOverallRate, 'bestMatchups' => $bestMatchups
        ]);
    }
}
