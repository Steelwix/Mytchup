<?php

    namespace App\Service;

    use App\Entity\Champion;
    use App\Entity\Matchup;
    use App\Entity\Pick;
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;

    class DataService
    {

        private EntityManagerInterface $em;

        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }

        public function handleCellData($coordinate, $formula){
            dd($coordinate, $formula);
        }

    }