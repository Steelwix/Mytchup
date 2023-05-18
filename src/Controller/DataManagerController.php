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
}
