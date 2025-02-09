<?php

    namespace App\Service;

    use App\Entity\Champion;
    use App\Entity\Matchup;
    use App\Entity\Pick;
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;

    class MatchupService
    {
        public function __construct(private EntityManagerInterface $em, PickService $pickService)
        {
            $this->em = $em;
            $this->pickService = $pickService;
        }
        public function processMatchup( $userChampion, $enemyChampion, $stats){
            if(!$userChampion instanceof Champion){
                $userChampion = $this->em->getRepository(Champion::class)->findOneBy(['name' => $userChampion]);
                if(!$userChampion){
                    throw new \Exception("user champion unknown for matchup process");
                }
            }
            if(!$enemyChampion instanceof Champion){
                $enemyChampion = $this->em->getRepository(Champion::class)->findOneBy(['name' => $enemyChampion]);
                if(!$userChampion){
                    throw new \Exception("enemy champion unknown");
                }
            }
            $pick = $this->pickService->processPick($userChampion);
            $matchup = $this->em->getRepository(Matchup::class)->findMatchupByPickAndEnemy($pick, $enemyChampion);
            if(!$matchup){
                $this->createMatchup($pick,$enemyChampion, $stats);
            }else{
                $this->updateMatchup($matchup, $stats);
            }
        }
        public function createMatchup($pick, $enemyChampion, array $stats){
            $matchup = new Matchup();
            $matchup->setOpponent($enemyChampion);
            $matchup->setPick($pick);
            $matchup->setWonGames($stats['WG']);
            $matchup->setTotalGames(1);
            if(null != $stats['WL']){
                $matchup->setWonLanes($stats['WL']);
                $matchup->setTotalLanes(1);
            }
            $this->em->persist($matchup);
            $this->em->flush();

        }

        public function updateMatchup($matchup, array $stats){
            $matchup->setWonGames($matchup->getWonGames() + $stats['WG']);
            $matchup->setTotalGames($matchup->getTotalGames() + 1);
            if(null != $stats['WL']){
                $matchup->setWonLanes($matchup->getWonLanes() + $stats['WL']);
                $matchup->setTotalLanes($matchup->getTotalLanes() + 1);
            }
            $this->em->flush();
        }
    }