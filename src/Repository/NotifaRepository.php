<?php

namespace App\Repository;

use App\Entity\Notifa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notifa>
 *
 * @method Notifa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notifa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notifa[]    findAll()
 * @method Notifa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notifa::class);
    }

//    /**
//     * @return Notifa[] Returns an array of Notifa objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notifa
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
