<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Entity\Matchup;
use App\Entity\Pick;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matchup>
 *
 * @method Matchup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matchup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matchup[]    findAll()
 * @method Matchup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matchup::class);
    }

    public function save(Matchup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Matchup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Retourne le matchup d'un pick et d'un champion
    public function findMatchupByPickAndEnemy(Pick $pick, Champion $enemy, $addSuggestions = false)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m')
            ->where('m.pick = :pick')
            ->andWhere('m.opponent = :enemy')
            ->setParameters([
                                'pick'  => $pick,
                                'enemy' => $enemy
                            ]);

        $bestMatchup[] = $qb->getQuery()->getOneOrNullResult();
        if (!$addSuggestions) {
            return $bestMatchup;
        }

        if (!$bestMatchup) {
            $bestMatchup[] = $this->findBestMatchupForThisOpponent($pick->getPlayer(), $enemy);
        }
            
        //TODO: Ajouter 4 suggestions de champions
            return $bestMatchup;
        }




    // Retourne les 5 meilleurs matchups pour un utilisateur et un champion
    public function findBestMatchupForThisOpponent(User $user, Champion $enemy)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m')
            ->leftJoin('m.pick', 'p')
            ->where('p.player = :user')
            ->andWhere('m.opponent = :enemy')
            ->setParameters([
                                'enemy' => $enemy,
                                'user' => $user,
                            ]);

        $allMatchups = $qb->getQuery()->getResult();

        // Tri des matchups par taux de victoire (win rate) décroissant
        usort($allMatchups, function ($a, $b) {
            $winRateA = $a->getTotalGames() > 0 ? ($a->getWonGames() / $a->getTotalGames()) * 100 : 0;
            $winRateB = $b->getTotalGames() > 0 ? ($b->getWonGames() / $b->getTotalGames()) * 100 : 0;
            return $winRateB <=> $winRateA; // Tri décroissant
        });

        // Retourne les 6 meilleurs matchups
        return array_slice($allMatchups, 0, 6);
    }

//Retourne les 5 meilleurs matchups avec ce pick
    public function findBestMatchupForThisPick(Pick $pick){}

//    /**


//     * @return Matchup[] Returns an array of Matchup objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Matchup
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
