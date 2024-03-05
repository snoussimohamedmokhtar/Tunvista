<?php

namespace App\Repository;

use App\Entity\Annonce;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    // AnnonceRepository.php
    // AnnonceRepository.php
    public function findBySearchTermAndTitre($searchTerm, $titre)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('a.titre_a LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if (!empty($titre)) {
            $queryBuilder->andWhere('a.titre_a = :titre')
                ->setParameter('titre', $titre);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getStatisticsByCity(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.ville_a, COUNT(a.id_annonce) as total')
            ->groupBy('a.ville_a')
            ->getQuery()
            ->getResult();
    }
    public function deleteExpiredAnnouncements()
    {
        // Get the current date and time
        $currentDate = new DateTime();

        // Create a query builder
        $queryBuilder = $this->createQueryBuilder('a');

        // Add a condition to filter announcements with expiration date before the current date
        $queryBuilder
            ->where('a.date_debut < :currentDate')
            ->setParameter('currentDate', $currentDate);

        // Get the matching announcements
        $expiredAnnouncements = $queryBuilder->getQuery()->getResult();

        // Delete each expired announcement
        foreach ($expiredAnnouncements as $expiredAnnouncement) {
            $this->_em->remove($expiredAnnouncement);
        }

        // Execute the delete operations
        $this->_em->flush();
    }




//    /**
//     * @return Annonce[] Returns an array of Annonce objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id_annonce', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
