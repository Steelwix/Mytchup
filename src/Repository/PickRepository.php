<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Entity\Pick;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pick>
 *
 * @method Pick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pick[]    findAll()
 * @method Pick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pick::class);
    }

    public function save(Pick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Pick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findPickByUserAndChampion(User $user,  Champion $picked)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p')
            ->where('p.player = :user')
            ->andWhere('p.champion = :champion')
            ->setParameters([
                                'user' => $user,
                                'champion' => $picked
                            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByUserAndChampion(User $user, Champion $champion){
        return $this->createQueryBuilder('p')
            ->where('p.player = :user')
            ->andWhere('p.champion = :champion')
            ->setParameter('user', $user)
            ->setParameter('champion', $champion)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Pick[] Returns an array of Pick objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pick
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
