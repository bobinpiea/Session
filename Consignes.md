Vous avez la charge de développer une application permettant de gérer des sessions
de formations pour les administrateurs d’un centre de formation

L’application sera seulement accessible par les administrateurs.

Chaque session a un nombre de place limité et est fixé à une date précise de début et de fin. ok 

On doit pouvoir savoir à tout moment le nombre de places restantes en fonction du nombre de
personnes inscrites. ok 



Reqêtes SQL  : 


NOW : 

SELECT * FROM session
WHERE date_fin < '2025-07-11';

SELECT * FROM session
WHERE date_debut < '2025-07-11';

BETWEEN : 

SELECT * 
FROM session
WHERE date_debut 
BETWEEN '2025-03-11' AND '2025-07-11';


  OU

SELECT * 
FROM session
WHERE date_debut <= '2025-07-11'
  AND date_fin >= '2025-07-11';


AFTER : 

SELECT * FROM session
WHERE date_debut > '2025-07-11';


-------------------

NOW : 

SELECT * FROM session
WHERE date_fin < now();



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


      //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($nom): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.nom = :id')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(6)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
