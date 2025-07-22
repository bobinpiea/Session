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



public function findPastSessions(): array
{
    return $this->createQueryBuilder('s')
        ->where('s.dateFin < :now')
        ->setParameter('now', now())
        ->orderBy('s.dateFin', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findCurrentSessions(): array
{
    return $this->createQueryBuilder('s')
        ->where('s.dateDebut <= :now')
        ->andWhere('s.dateFin >= :now')
        ->setParameter('now', now())
        ->orderBy('s.dateDebut', 'ASC')
        ->getQuery()
        ->getResult();
}

public function findUpcomingSessions(): array
{
    return $this->createQueryBuilder('s')
        ->where('s.dateDebut > :now')
        ->setParameter('now', now())
        ->orderBy('s.dateDebut', 'ASC')
        ->getQuery()
        ->getResult();
}





// 📁 Dans StagiaireRepository.php

public function findNonInscrits(Session $session): array
{
    $em = $this->getEntityManager();

    // Sous-requête : récupérer les stagiaires déjà inscrits à la session
    $sub = $em->createQueryBuilder()
        ->select('s.id')
        ->from('App\Entity\Stagiaire', 's')
        ->leftJoin('s.sessions', 'se')
        ->where('se = :session');

    // Requête principale : tous les stagiaires NON inscrits
    $qb = $em->createQueryBuilder()
        ->select('st')
        ->from('App\Entity\Stagiaire', 'st')
        ->where($qb->expr()->notIn('st.id', $sub->getDQL()))
        ->setParameter('session', $session)
        ->orderBy('st.nom', 'ASC');

    return $qb->getQuery()->getResult();
}




#[Route('/session/{idSession}/remove-stagiaire/{idStagiaire}', name: 'remove_stagiaire_session')]
public function removeStagiaireFromSession(
    EntityManagerInterface $em,
    $idSession,
    $idStagiaire,
    SessionRepository $sr,
    StagiaireRepository $stRep
): Response {
    $session = $sr->find($idSession);
    $stagiaire = $stRep->find($idStagiaire);

    if ($session && $stagiaire) {
        $session->removeStagiaire($stagiaire);

        // Mise à jour du nombre de places réservées
        if ($session->getNbPlaces() > 0) {
            $session->setNbPlaces($session->getNbPlaces() - 1);
        }

        $em->persist($session);
        $em->flush();

        $this->addFlash('success', 'Stagiaire retiré de la session avec succès.');
    }

    return $this->redirectToRoute('show_session', ['id' => $idSession]);
}