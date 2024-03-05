<?php

namespace App\Repository;

use App\Entity\Evenement;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function findBySearchTermAndTitre($searchTerm, $titre)
    {
        $queryBuilder = $this->createQueryBuilder('e');

        if (!empty($searchTerm)) {
            $queryBuilder->andWhere('e.titre_e LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if (!empty($type)) {
            $queryBuilder->andWhere('e.titre_e = :type')
                ->setParameter('titre', $titre);
        }

        return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return Evenement[] Returns an array of Evenement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id_evenemet', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function deleteExpiredEvents()
    {
        // Get the current date and time
        $currentDate = new DateTime();

        // Create a query builder
        $queryBuilder = $this->createQueryBuilder('e');

        // Add a condition to filter announcements with expiration date before the current date
        $queryBuilder
            ->where('e.date_deb < :currentDate')
            ->setParameter('currentDate', $currentDate);

        // Get the matching announcements
        $expiredEvents = $queryBuilder->getQuery()->getResult();

        // Delete each expired announcement
        foreach ($expiredEvents as $expiredEvent) {
            $this->_em->remove($expiredEvent);
        }

        // Execute the delete operations
        $this->_em->flush();
    }
}
