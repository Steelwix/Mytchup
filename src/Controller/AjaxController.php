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
        elseif(!$pick){
            $bestMatchups = $this->em->getRepository(Matchup::class)->findBestMatchupForThisOpponent($this->getUser(), $encounter);
        }
        elseif(!$encounter){
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


}
