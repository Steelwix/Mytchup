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
            $matchup->setPick($pick[0]);
            $matchup->setWonGames($matches['WG']);
            $matchup->setWonLanes($matches['WL']);
            $matchup->setTotalGames($matches['TG']);
            $matchup->setTotalLanes($matches['TL']);
            $this->em->persist($matchup);

        }
            public function matchupExists(string $enemy, string $picked, User $user){
                $pickedChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $picked]);
                $enemyChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $enemy]);
                $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($user, $pickedChamp);
                return  $this->em->getRepository(Matchup::class)->findByOpponent($enemyChamp);

            }

        public function updateMatchup(string $enemy, array $matches, string $picked, User $user){
            $enemyChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $enemy]);
            $pickedChamp = $this->em->getRepository(Champion::class)->findOneBy(['name' => $picked]);
            $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($user, $pickedChamp);
            $matchup = $this->em->getRepository(Matchup::class)->findMatchupByPickAndEnemy($pick[0], $enemyChamp);
            $matchup[0]->setWonGames($matches['WG']);
            $matchup[0]->setWonLanes($matches['WL']);
            $matchup[0]->setTotalGames($matches['TG']);
            $matchup[0]->setTotalLanes($matches['TL']);
            $this->em->persist($matchup[0]);

        }


    }