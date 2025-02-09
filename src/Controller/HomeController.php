<?php

namespace App\Controller;

use App\Entity\Pick;
use App\Form\PushNewStatFormType;
use App\Service\API\GetAllChampsService;
use App\Service\MatchupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index(Request $request): Response
    {
        $globalWonGames = $globalWonLanes = $globalTotalGames = $globalTotalLanes = 0;
        $form = $this->createForm(PushNewStatFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $userChampion = $form->getData()['firstChampion'];
            $opponentChampion = $form->getData()['secondChampion'];
            $stats['WG'] = $form->getData()['game_won'];
            $stats['WL'] = $form->getData()['matchup_won'];
            $this->matchupManager->processMatchup($userChampion, $opponentChampion, $stats);
        }


            /** @var User $user */
            $user = $this->getUser();
            $picks = $user->getPicks();


            foreach ($picks as $pick) {
                $matchups = $pick->getMatchups();
                foreach ($matchups as $matchup) {
                    $globalWonGames = $globalWonGames + $matchup->getWonGames();
                    $globalWonLanes = $globalWonLanes + $matchup->getWonLanes();
                    $globalTotalGames = $globalTotalGames + $matchup->getTotalGames();
                    $globalTotalLanes = $globalTotalLanes + $matchup->getTotalLanes();
                }
            }

        $globalWinRate = $globalLaneWinRate = 0;
        if ($globalTotalGames != 0) {
            $globalWinRate = ($globalWonGames / $globalTotalGames) * 100;
        }
        if ($globalTotalLanes != 0) {
            $globalLaneWinRate = ($globalWonLanes / $globalTotalLanes) * 100;
        }
        $globalOverallRate = ($globalWinRate + $globalLaneWinRate) / 2;
        $bestMatchups = $this->em->getRepository(Pick::class)->findMostPickedOfThisUser($this->getUser());
        return $this->render('home/index.html.twig', [
            'newGameStat' => $form->createView(), 'globalWonGames' => $globalWonGames, 'globalWonLanes' => $globalWonLanes,
            'globalTotalGames' => $globalTotalGames, 'globalTotalLanes' => $globalTotalLanes, 'globalWinRate' => $globalWinRate,
            'globalLaneWinRate' => $globalLaneWinRate, 'globalOverallRate' => $globalOverallRate, 'bestMatchups' => $bestMatchups
        ]);
    }
}
