<?php

namespace App\Repository;

use App\Entity\Hotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hotel>
 *
 * @method Hotel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hotel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hotel[]    findAll()
 * @method Hotel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HotelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotel::class);
    }
    public function gethotelStatistics()
{
    return $this->createQueryBuilder('h')
        ->select('h.Nbre_etoile, COUNT(h.idH) as count')
        ->groupBy('h.Nbre_etoile')
        ->getQuery()
        ->getResult();
}

public function findBySearchQuery(?string $query): array
    {
        if (!$query) {
            return $this->findAll();
        }

        return $this->createQueryBuilder('h')
            ->andWhere('h.idH LIKE :query OR h.Nom_hotel LIKE :query OR h.Nbre_etoile LIKE :query OR h.Adresse_hotel LIKE :query OR h.prix_nuit LIKE :query OR h.image LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllAscending(string $criteria): array
    {
        return $this->createQueryBuilder('h')
            ->orderBy($criteria, 'ASC') // Replace 'fieldToSortBy' with the actual field name you want to sort by
            ->getQuery()
            ->getResult();
    }

    public function findAllDescending(string $criteria): array
    {
        return $this->createQueryBuilder('h')
            ->orderBy($criteria, 'DESC') // Replace 'fieldToSortBy' with the actual field name you want to sort by
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Hotel[] Returns an array of Hotel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Hotel
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
