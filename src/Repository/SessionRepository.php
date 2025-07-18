<?php

namespace App\Repository;

use now;
use App\Entity\Session;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

public function findPastSessions(): array
{
    return $this->createQueryBuilder('s')
        ->where('s.dateFin < :now')
        ->setParameter('now', new \DateTime()) 
        ->orderBy('s.dateFin', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findCurrentSessions(): array
{
    return $this->createQueryBuilder('s') // 's' = Session
        ->where('s.dateDebut <= :now') // filtre 1
        ->andWhere('s.dateFin >= :now') // filtre 2
        ->setParameter('now', new \DateTime()) // :now = aujourd’hui
        ->orderBy('s.dateDebut', 'ASC') // tri
        ->getQuery() // on construit
        ->getResult(); // on exécute et on reçoit un tableau 
    // Idem que dans SQl qd on prepare et on exécute ultérieurement 
}

public function findUpcomingSessions(): array
{
    return $this->createQueryBuilder('s')
        ->where('s.dateDebut > :now')
        ->setParameter('now', new \DateTime()) 
        ->orderBy('s.dateDebut', 'ASC')
        ->getQuery()
        ->getResult();
}







  
}
