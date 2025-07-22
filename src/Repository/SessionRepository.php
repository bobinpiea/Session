<?php

namespace App\Repository;

use now;
use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Programme;
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


    /* Objetif : Renvoyer tous les stagiaires qui ne sont pas inscrits 
    à la session dont l’id est $session_id. */

    /** Afficher les stagiaires non inscrits dans une session */
    public function findNonInscrits($session_id) // Reçoit l'identifiant d'une session en paramètre
    {
        $em = $this->getEntityManager(); // On récupère le gestionnaire (EntityManager) pour construire nos requêtes
        $sub = $em->createQueryBuilder(); // On construit une sous-requête pour récupérer les stagiaires inscrits à ladite session
        
        $qb = $sub; //  ???

        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('s') // On sélectionne les stagiaires ('s') qui sont reliés à cette session dont l'ID est celui recu en paramètre (plus haut)
            ->from('App\Entity\Stagiaire', 's') // On sélectionne l'entité stagiaire à partir duquel on a fait l'alias 
            ->leftJoin('s.sessions', 'se') // relie chaque stagiaire à sa/ses sessions 
            ->where('se.id = :id'); // à condition que  / filtre : seulement les stagiaires inscrits à la session donnée
        
        $sub = $em->createQueryBuilder(); // Nouvelle requete : On instancie à nouveau 

        // OBJECTIF : 
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie

        $sub->select('st') // On veut récupérer les stagiaires (alias 'st')

            ->from('App\Entity\Stagiaire', 'st') // Qui sont dans l'entité/table stagiaire
            ->where($sub->expr()->notIn('st.id', $qb->getDQL())) // On ne veut pas ceux qui sont dans larequete écrit plus haut | expr ? 

            // requête parametree
            ->setParameter('id', $session_id) // On donne la valeur du paramètre 'id'

            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('st.nom'); // on trie par ordre alphabétique 
        
        // renvoyer le résultat : On exécute la requete et on renvoie le tableau de résultat
        $query = $sub->getQuery(); // // Conversion en requete DQL 
        return $query->getResult(); // On retourne le resultat et exécution de la requete 
    }
    
  /*  
    // OBJECTIF
    // AFFICHER LES MODULES QUI NE SONT PAS DANS UNE SESSION 
    public function findNonProgrammes($session_id) {
       
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub; // ???

        // Afficher les modules qui sont dans une session
        $qb ->select('m')
            ->from('App\Entity\Module', 'm')
            ->leftJoin('Programme', 'p')
            ->where('p.session_id = :id'); //???

        $sub = $em->createQueryBuilder();

    // Requête principale : récupérer tous les modules SAUF ceux de la premiere requetes

        $sub->select('mod')
            ->from('App\Entity\Module', 'mod')
            ->where($sub->expr()->notIn('mod.id', $qb->getDQL()))
            ->setParameter('id', $session_id)
            ->orderBy('mod.nom');
            
        $query = $sub->getQuery();
        return $query->getResult();
    }
    
 */

/*
    public function findNonProgrammes($session_id) {
    $em = $this->getEntityManager();

    // Étape 1 : récupérer les modules DÉJÀ programmés pour cette session
    $qb = $em->createQueryBuilder();
    $qb->select('m')
        ->from('App\Entity\Module', 'm')
        ->leftJoin('programme', 'pr')
        ->where('pr.session = :id');

    // Étape 2 : récupérer tous les modules SAUF ceux trouvés dans la sous-requête
    $sub = $em->createQueryBuilder();
    $sub->select('mod')
        ->from('App\Entity\Module', 'mod')
        ->where($sub->expr()->notIn('mod.id', $qb->getDQL()))
        ->setParameter('id', $session_id)
        ->orderBy('mod.nom');

    $query = $sub->getQuery();
    return $query->getResult();
}

 */
public function findNonProgrammes($session_id) {
    $em = $this->getEntityManager();

    // Étape 1 : récupérer les modules déjà programmés
    $qb = $em->createQueryBuilder();
    $qb->select('m')
        ->from('App\Entity\Module', 'm')
        ->leftJoin('App\Entity\Programme', 'pr', 'WITH', 'pr.module = m')
        ->where('pr.session = :id');

    // Étape 2 : récupérer les modules NON programmés
    $sub = $em->createQueryBuilder();
    $sub->select('mod')
        ->from('App\Entity\Module', 'mod')
        ->where($sub->expr()->notIn('mod.id', $qb->getDQL()))
        ->setParameter('id', $session_id)
        ->orderBy('mod.nom');

    $query = $sub->getQuery();
    return $query->getResult();
}
}
