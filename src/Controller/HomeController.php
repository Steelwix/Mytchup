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
            $wonGames = $wonLanes = $totalGames = $totalLanes = 0;
            foreach ($picks as $pick) {
                $matchups = $pick->getMatchups();
                foreach ($matchups as $matchup) {
                    $wonGames = $wonGames + $matchup->getWonGames();
                    $wonLanes = $wonLanes + $matchup->getWonLanes();
                    $totalGames = $totalGames + $matchup->getTotalGames();
                    $totalLanes = $totalLanes + $matchup->getTotalLanes();
                }
            }
        }
        $options = array('Option 1', 'Option 2', 'Option 3', 'Option 4');
        return $this->render('home/index.html.twig', ['newGameStat' => $form->createView()]);
    }
}
