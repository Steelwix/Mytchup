<?php

    namespace App\Service;

    use App\Entity\Champion;
    use App\Entity\Pick;
    use Doctrine\ORM\EntityManagerInterface;

    class PickService
    {
        public function __construct(private EntityManagerInterface $em)
        {
            $this->em = $em;
        }

        public function processPick($userChampion)
        {
            if(!$userChampion instanceof Champion){
                $userChampion = $this->em->getRepository(Champion::class)->findOneBy(['name' => $userChampion]);
                if(!$userChampion){
                    throw new \Exception("user champion unknown for pick process");
                }
                $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion($this->getUser(), $userChampion);
                if(!$pick){
                    $pick = $this->createPick($userChampion);
                }
                return $pick;
            }
        }

        public function createPick(Champion $champion){
            $pick = new Pick();
            $pick->setChampion($champion);
            $pick->setUser($this->getUser());
            $this->em->persist($pick);
            $this->em->flush();
            return $pick;
        }
    }