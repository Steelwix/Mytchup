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

    public function findBestWinrate(array $picks): ?Pick
    {
        $bestPick = null;
        $bestWinrate = 0;

        foreach ($picks as $pick) {
            $query = $this->_em->createQuery('
            SELECT SUM(m.totalGames) AS totalGames, SUM(m.wonGames) AS wonGames
            FROM App\Entity\Matchup m
            WHERE m.pick = :pick
        ')->setParameter('pick', $pick);

            $result = $query->getSingleResult();

            if ($result['totalGames'] > 0) {
                $winrate = ($result['wonGames'] / $result['totalGames']) * 100;
                if ($winrate > $bestWinrate) {
                    $bestWinrate = $winrate;
                    $bestPick = $pick;
                }
            }
        }

        return $bestPick;
    }

    public function findMostPickedOfThisUser(User $user = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('m')
            ->from('App\Entity\Matchup', 'm')
            ->leftJoin('m.pick', 'p')
            ->orderBy('m.totalGames', 'DESC')
            ->addOrderBy('m.wonGames', 'DESC')
            ->setMaxResults(6);

        if ($user !== null) {
            $qb->where('p.player = :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
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
