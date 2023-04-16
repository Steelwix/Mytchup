<?php

namespace App\Controller;

use App\Entity\Champion;
use App\Form\PushNewStatFormType;
use App\Service\API\GetAllChampsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{

    private $allChampsService;
    private $em;
    public function __construct(GetAllChampsService $allChampsService, EntityManagerInterface $em)
    {
        $this->allChampsService = $allChampsService;
        $this->em = $em;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $championRepository = $this->em->getRepository(Champion::class);
        $storedChamps = $championRepository->findAll();

        if ($this->getUser() != null) {


            /** @var User $user */
            $user = $this->getUser();
            $picks = $user->getPicks();

            $form = $this->createForm(PushNewStatFormType::class);
            $globalWonGames = $globalWonLanes = $globalTotalGames = $globalTotalLanes = 0;
            foreach ($picks as $pick) {
                $matchups = $pick->getMatchups();
                foreach ($matchups as $matchup) {
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
            'globalLaneWinRate' => $globalLaneWinRate, 'globalOverallRate' => $globalOverallRate
        ]);
    }
}
