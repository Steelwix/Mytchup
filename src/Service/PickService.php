<?php

    namespace App\Service;

    use App\Entity\Champion;
    use App\Entity\Pick;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\SecurityBundle\Security;

    class PickService
    {
        public function __construct(private EntityManagerInterface $em, Security $security)
        {
            $this->em = $em;
            $this->security = $security;
        }

        public function processPick(Champion $userChampion)
        {

                $pick = $this->em->getRepository(Pick::class)->findPickByUserAndChampion( $this->security->getUser(), $userChampion);
                if(!$pick){
                    $pick = $this->createPick($userChampion);
                }
                return $pick;
        }

        public function createPick(Champion $champion){
            $pick = new Pick();
            $pick->setChampion($champion);
            $pick->setPlayer( $this->security->getUser());
            $this->em->persist($pick);
            $this->em->flush();
            return $pick;
        }
    }