<?php

namespace App\Controller;

use App\Entity\Champion;
use App\Entity\Matchup;
use App\Entity\Pick;
use App\Service\MatchupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AjaxController extends AbstractController
{

    private $em;
    private $serializer;
    private $matchupService;

    public function __construct(EntityManagerInterface $em,
                                SerializerInterface $serializer,
                                MatchupService $matchupService)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->matchupService = $matchupService;
    }

//    #[Route('/ajax/my-pick', name: 'app_ajax_my_pick')]
//    public function getChampion(Request $request): JsonResponse
//    {
//
//        $championName = json_decode($request->getContent(), true);
//        $pickWonGames = $pickWonLanes = $pickTotalGames = $pickTotalLanes = 0;
//        $pickWinRate = $pickLaneWinRate = $pickOverallRate = 0;
//        $champion = $this->em->getRepository(Champion::class)->findOneBy(['name' => $championName['champion']]);
//        /** @var User $user */
//        $user = $this->getUser();
//        $picks = $user->getPicks();
//        $bestMatchups = [];
//        foreach ($picks as $pick) {
//            if ($pick->getChampion()->getId() === $champion->getId()) {
//                $matchups = $pick->getMatchups();
//
//                foreach ($matchups as $matchup) {
//                    $winRate = ($matchup->getWonGames() / $matchup->getTotalGames()) * 100;
//                    if(count($bestMatchups) < 6){
//                        $bestMatchups[] = $this->matchupService->defineBestMatchups($matchup, $winRate, $pick);
//                    }
//                    else {
//
//                        foreach ($bestMatchups as  $key => $best){
//                            if($best['win_rate'] < $winRate){
//                                break;
//                            }
//                        }
//                        $bestMatchups[$key] = $this->matchupService->defineBestMatchups($matchup, $winRate, $pick);
//                    }
//                    $pickWonGames = $pickWonGames + $matchup->getWonGames();
//                    $pickWonLanes = $pickWonLanes + $matchup->getWonLanes();
//                    $pickTotalGames = $pickTotalGames + $matchup->getTotalGames();
//                    $pickTotalLanes = $pickTotalLanes + $matchup->getTotalLanes();
//                }
//
//                if ($pickTotalGames != 0) {
//                    $pickWinRate = ($pickWonGames / $pickTotalGames) * 100;
//                }
//                if ($pickTotalLanes != 0) {
//                    $pickLaneWinRate = ($pickWonLanes / $pickTotalLanes) * 100;
//                }
//                $pickOverallRate = ($pickWinRate + $pickLaneWinRate) / 2;
//            }
//        }
//        $champion = $this->serializer->serialize($champion, 'json', ['groups' => ['getChampion']]);
//        $responseData = [
////            'champion'        => $champion,
////            'pickWonGames'    => $pickWonGames,
////            'pickWonLanes'    => $pickWonLanes,
////            'pickTotalGames'  => $pickTotalGames,
////            'pickTotalLanes'  => $pickTotalLanes,
////            'pickWinRate'     => $pickWinRate,
////            'pickLaneWinRate' => $pickLaneWinRate,
////            'pickOverallRate' => $pickOverallRate,
//            'bestMatchups' => $bestMatchups,
//        ];
//        return new JsonResponse(
//            $responseData,
//            Response::HTTP_OK,
//            ['Content-Type' => 'application/json']
//        );
//    }

    #[Route('/ajax/make-stats', name: 'app_ajax_make_stats')]
    public function getEncounter(Request $request): JsonResponse
    {
        $datas = json_decode($request->getContent(), true);
        $encounterName = $datas["encounter"];
        $championName = $datas["pick"];
        $encounter = $this->em->getRepository(Champion::class)->findOneBy(['name' => $encounterName]);
        $champion = $this->em->getRepository(Champion::class)->findOneBy(['name' => $championName]);
        $bestMatchups= [];
        $pick = null;

        if($champion){
            $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($this->getUser(), $champion);
        }
        if($encounter && $pick){
            $bestMatchups = $this->em->getRepository(Matchup::class)->findMatchupByPickAndEnemy($pick, $encounter, true);
        }
        if(!$pick){
            $bestMatchups = $this->em->getRepository(Matchup::class)->findBestMatchupForThisOpponent($this->getUser(), $encounter);
        }
        if(!$encounter){
            $bestMatchups = $this->em->getRepository(Matchup::class)->findBestMatchupForThisPick($pick);
        }
        $bestMatchups = $this->serializer->serialize($bestMatchups, 'json', ['groups' => ['getEncounter']]);

        $responseData = [
//            'encounterWonGames'    => $encounterWonGames,
//            'encounterWonLanes'    => $encounterWonLanes,
//            'encounterTotalGames'  => $encounterTotalGames,
//            'encounterTotalLanes'  => $encounterTotalLanes,
//            'encounterWinRate'     => $encounterWinRate,
//            'encounterLaneWinRate' => $encounterLaneWinRate,
//            'encounterOverallRate' => $encounterOverallRate,
            'bestMatchups' => $bestMatchups,
        ];
        dump($responseData);
        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

//    #[Route('/ajax/my-matchup', name: 'app_ajax_my_matchup')]
//    public function getMatchup(Request $request): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//        $playerPick = $this->em->getRepository(Champion::class)->findOneByName(['name' => $data['pick']]);
//        $encounter = $this->em->getRepository(Champion::class)->findOneByName(['name' => $data['encounter']]);
//        $matchups = [];
//        /** @var User $user */
//        $user = $this->getUser();
//        $wonGames = $wonLanes = $totalGames = $totalLanes = $winRate = $winLaneRate = $overallWinRate = 0;
//        $qb = $this->em->createQueryBuilder();
//        $qb->select('p')
//            ->from(Pick::class, 'p')
//            ->where('p.player = :user')
//            ->andWhere('p.champion = :champion')
//            ->setParameters([
//                                'user' => $user,
//                                'champion' => $playerPick
//                            ]);
//
//        $picks = $qb->getQuery()->getResult();
//        foreach ($picks as $pick){
//        $qb = $this->em->createQueryBuilder();
//        $qb->select('m')
//            ->from(Matchup::class, 'm')
//            ->join('m.pick', 'p')
//            ->where('p.id = :pick_id')
//            ->andWhere('m.opponent = :opponent')
//            ->setParameters([
//                                'pick_id' => $pick->getId(),
//                                'opponent' => $encounter
//                            ]);
//
//        $matchups = $qb->getQuery()->getResult();
//            foreach ($matchups as $matchup){
//                $wonGames += $matchup->getWonGames();
//                $wonLanes += $matchup->getWonLanes();
//                $totalGames += $matchup->getTotalGames();
//                $totalLanes += $matchup->getTotalLanes();
//            }
//        }
//        if ($totalGames != 0) {
//            $winRate = ($wonGames / $totalGames) * 100;
//        }
//        if ($totalLanes != 0) {
//            $winLaneRate = ($wonLanes / $totalLanes) * 100;
//        }
//        $overallWinRate = ($winLaneRate + $winRate) / 2;
//        $playerPick = $this->serializer->serialize($playerPick, 'json', ['groups' => ['getChampion']]);
//        $encounter = $this->serializer->serialize($encounter, 'json', ['groups' => ['getChampion']]);
//        $responseData = [
//            'wonGames'            => $wonGames,
//            'wonLanes'    => $wonLanes,
//            'totalGames'    => $totalGames,
//            'totalLanes'  => $totalLanes,
//            'pick' =>  $playerPick,
//            'encounter' => $encounter,
//            'winRate' => $winRate,
//            'winLaneRate' => $winLaneRate,
//            'overallWinrate'=> $overallWinRate];
//        return new JsonResponse(
//            $responseData,
//            Response::HTTP_OK,
//            ['Content-Type' => 'application/json']
//        );
//    }
    #[Route('/ajax/data-insert', name: 'app_ajax_data_insert')]
    public function insertData(Request $request): JsonResponse
    {
        $datas = json_decode($request->getContent(), true);
        $matchups = $return = [];
        foreach ($datas as $data){
            $return[] = $data;

            $matchups[$data['id']]['match_case'][$data['matchCase']] = $data['value'];
            $match_check = $matchups[$data['id']]['match_case'];
            $wantedMatches = ["WG", "TG", "WL", "TL"];
            $count = 0;

            foreach ($wantedMatches as $key) {
                if (array_key_exists($key, $match_check)) {
                    $count++;
                }
            }

            if($count == 4 && $match_check['TG'] > 0){
                if($this->matchupService->matchupExists($data['id'], $data['class'], $this->getUser())){
                    $this->matchupService->updateMatchup($data['id'], $match_check, $data['class'], $this->getUser());
            }
            else {
                $this->matchupService->createMatchup($data['id'], $match_check, $data['class'], $this->getUser());
            }
            }
        }
        $this->em->flush();
        return new JsonResponse($return,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
