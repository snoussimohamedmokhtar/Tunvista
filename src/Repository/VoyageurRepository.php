<?php

namespace App\Repository;

use App\Entity\Voyageur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voyageur>
 *
 * @method Voyageur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voyageur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voyageur[]    findAll()
 * @method Voyageur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoyageurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voyageur::class);
    }

    public function findAllAscending(string $criteria): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy($criteria, 'ASC') // Replace 'fieldToSortBy' with the actual field name you want to sort by
            ->getQuery()
            ->getResult();
    }

    public function findAllDescending(string $criteria): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy($criteria, 'DESC') // Replace 'fieldToSortBy' with the actual field name you want to sort by
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Voyageur[] Returns an array of Voyageur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Voyageur
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
