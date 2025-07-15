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
    return $this->createQueryBuilder('s')
        ->where('s.dateDebut <= :now')
        ->andWhere('s.dateFin >= :now')
        ->setParameter('now', new \DateTime()) 
        ->orderBy('s.dateDebut', 'ASC')
        ->getQuery()
        ->getResult();
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
