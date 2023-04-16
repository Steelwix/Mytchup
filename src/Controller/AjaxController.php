<?php

namespace App\Controller;

use App\Entity\Champion;
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

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }
    #[Route('/ajax/my-pick', name: 'app_ajax_my_pick')]
    public function getChampion(Request $request): JsonResponse
    {

        $championName = json_decode($request->getContent(), true);
        $pickWonGames = $pickWonLanes = $pickTotalGames = $pickTotalLanes = 0;
        $pickWinRate = $pickLaneWinRate = $pickOverallRate = 0;
        $champion = $this->em->getRepository(Champion::class)->findOneBy(['name' => $championName['champion']]);
        /** @var User $user */
        $user = $this->getUser();
        $picks = $user->getPicks();
        foreach ($picks as $pick) {
            if ($pick->getChampion()->getId() === $champion->getId()) {
                $matchups = $pick->getMatchups();

                foreach ($matchups as $matchup) {
                    $pickWonGames = $pickWonGames + $matchup->getWonGames();
                    $pickWonLanes = $pickWonLanes + $matchup->getWonLanes();
                    $pickTotalGames = $pickTotalGames + $matchup->getTotalGames();
                    $pickTotalLanes = $pickTotalLanes + $matchup->getTotalLanes();
                }

                if ($pickTotalGames != 0) {
                    $pickWinRate = ($pickWonGames / $pickTotalGames) * 100;
                }
                if ($pickTotalLanes != 0) {
                    $pickLaneWinRate = ($pickWonLanes / $pickTotalLanes) * 100;
                }
                $pickOverallRate = ($pickWinRate + $pickLaneWinRate) / 2;
            }
        }
        $champion = $this->serializer->serialize($champion, 'json', ['groups' => ['getChampion']]);
        $responseData = [
            'champion' => $champion,
            'pickWonGames' => $pickWonGames,
            'pickWonLanes' => $pickWonLanes,
            'pickTotalGames' => $pickTotalGames,
            'pickTotalLanes' => $pickTotalLanes,
            'pickWinRate' => $pickWinRate,
            'pickLaneWinRate' => $pickLaneWinRate,
            'pickOverallRate' => $pickOverallRate
        ];
        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
    #[Route('/ajax/my-encounter', name: 'app_ajax_my_encounter')]
    public function getEncounter(Request $request): JsonResponse
    {
        $encounterName = json_decode($request->getContent(), true);
        $encounterWonGames = $encounterWonLanes = $encounterTotalGames = $encounterTotalLanes = 0;
        $encounterWinRate = $encounterLaneWinRate = $encounterOverallRate = 0;
        $encounter = $this->em->getRepository(Champion::class)->findOneByName(['name' => $encounterName['encounter']]);
        /** @var User $user */
        $user = $this->getUser();
        $picks = $user->getPicks();
        foreach ($picks as $pick) {
            $matchups = $pick->getMatchups();
            foreach ($matchups as $matchup) {
                if ($encounter->getName() == $matchup->getOpponent()->getName()) {
                    $encounterWonGames = $encounterWonGames + $matchup->getWonGames();
                    $encounterWonLanes = $encounterWonLanes + $matchup->getWonLanes();
                    $encounterTotalGames = $encounterTotalGames + $matchup->getTotalGames();
                    $encounterTotalLanes = $encounterTotalLanes + $matchup->getTotalLanes();
                }
            }
        }
        if ($encounterTotalGames != 0) {
            $encounterWinRate = ($encounterWonGames / $encounterTotalGames) * 100;
        }
        if ($encounterTotalLanes != 0) {
            $encounterLaneWinRate = ($encounterWonLanes / $encounterTotalLanes) * 100;
        }
        $encounterOverallRate = ($encounterWinRate + $encounterLaneWinRate) / 2;

        $encounter = $this->serializer->serialize($encounter, 'json', ['groups' => ['getEncounter']]);
        $responseData = [
            'encounter' => $encounter,
            'encounterWonGames' => $encounterWonGames,
            'encounterWonLanes' => $encounterWonLanes,
            'encounterTotalGames' => $encounterTotalGames,
            'encounterTotalLanes' => $encounterTotalLanes,
            'encounterWinRate' => $encounterWinRate,
            'encounterLaneWinRate' => $encounterLaneWinRate,
            'encounterOverallRate' => $encounterOverallRate
        ];
        return new JsonResponse(
            $responseData,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
