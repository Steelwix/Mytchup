<?php

    namespace App\Service;

    use App\Entity\Champion;
    use App\Entity\Matchup;
    use App\Entity\Pick;
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;

    class MatchupService
    {
        private $em;
        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
            // Service constructor
        }

        public function createMatchup(string $enemy, array $matches, string $picked, User $user){
            $matchup = new Matchup();
            $enemyChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $enemy]);
            $matchup->setOpponent($enemyChamp);
            $pickedChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $picked]);
            $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($user, $pickedChamp);
            $matchup->setPick($pick);
            $matchup->setWonGames($matches['WG']);
            $matchup->setWonLanes($matches['WL']);
            $matchup->setTotalGames($matches['TG']);
            $matchup->setTotalLanes($matches['TL']);
            $this->em->persist($matchup);
            $this->em->flush();

        }
            public function matchupExists(string $enemy, string $picked, User $user){
                $pickedChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $picked]);
                $enemyChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $enemy]);
                $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($user, $pickedChamp);
                $matchupRepository = $this->em->getRepository(Matchup::class);

                $result = $matchupRepository->createQueryBuilder('m')
                    ->where('m.pick = :pick')
                    ->andWhere('m.opponent =  :opponent')
                    ->setParameters(['pick' => $pick, 'opponent' => $enemyChamp])
                    ->getQuery()
                    ->getOneOrNullResult();

                if($result){
                    return true;
                }
                return false;

            }

        public function updateMatchup(string $enemy, array $matches, string $picked, User $user){
            $enemyChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $enemy]);
            $pickedChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $picked]);
            $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($user, $pickedChamp);
            $matchup = $this->em->getRepository(Matchup::class)->findMatchupByPickAndEnemy($pick, $enemyChamp);
            $matchup->setWonGames($matches['WG']);
//            $matchup[0]->setWonLanes($matches['WL']);
//            $matchup[0]->setTotalGames($matches['TG']);
//            $matchup[0]->setTotalLanes($matches['TL']);
//           $this->em->flush();

        }
        public function getMatchupsFromUser(User $user){
            $picks = $user->getPicks();
            $matchups = [];
            foreach ($picks as $pick){
                $matchups[] = $pick->getMatchups();
            }
            return $matchups;
        }

        public function filterMatchupsByChampions(array $matchups, Champion $pick, Champion $opponent){
            foreach ($matchups as $matchupByPick){
                foreach ($matchupByPick as $matchup){
                    $matchup_pick = $matchup->getPick();
                    $matchup_pick_champion = $matchup_pick->getChampion();
                    $matchup_opponent = $matchup->getOpponent();
                    if($matchup_pick_champion === $pick && $matchup_opponent === $opponent){
                        return $matchup;
                    }
                }}
            return false;
        }

        public function filterNewMatchupsByChampions(array $matchups, string $pick, string $opponent){
            foreach ($matchups as $matchup){

                if($matchup['pick'] == $pick && $matchup['opponent'] == $opponent){
                    return $matchup;
                }
            }
            return false;
        }

        public function defineBestMatchups(Matchup $matchup, int $winRate, Pick $pick,){
            $return['champion'] = $matchup->getOpponent()->getName();
            $return['win_rate'] = $winRate;
            $return['playing'] = $pick->getChampion()->getName();
            return $return;
        }


    }