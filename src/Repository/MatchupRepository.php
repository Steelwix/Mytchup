<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Entity\Matchup;
use App\Entity\Pick;
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
    public function findMatchupByPickAndEnemy(Pick $pick, Champion $enemy)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m')
            ->where('m.pick = :pick')
            ->andWhere('m.opponent = :enemy')
            ->setParameters([
                                'pick' => $pick,
                                'enemy' => $enemy
                            ]);

        return $qb->getQuery()->getResult();
    }
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
