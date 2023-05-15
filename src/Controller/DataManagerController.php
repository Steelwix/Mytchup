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


class DataManagerController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/data', name: 'app_data_manager')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(DataManagerFormType::class);
        $form->handleRequest($request);
        $champions = $this->em->getRepository(Champion::class)->findAll();
        /** @var User $user */
        $user = $this->getUser();
        $picks = $user->getPicks();
        //Display 4 forms row foreach Champions, and in view just make foreach champ
        //Associate the line number to the champion number
//        $playerStat = [];
//        foreach ($champions as $champion){
//            $name = $champion->getName();
//            getMatchup
//            $gameStats = ... ;
//            $playerStat[$name] = $gameStats
//
//
//        }
        //Select your champ by clicking on the played champ (display only already played champ)
        //

        return $this->render('data_manager/index.html.twig', ['picks' => $picks]);
    }
}
