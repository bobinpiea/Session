<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use Doctrine\ORM\EntityManager;
use App\Repository\SessionRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class SessionController extends AbstractController
{
    /* LISTE  */
        #[Route('/session', name: 'app_session')]
        public function index(SessionRepository $sessionRepository): Response
        {   
            $sessionsPassees = $sessionRepository->findPastSessions();
            $sessionsEnCours = $sessionRepository->findCurrentSessions();
            $sessionsAVenir = $sessionRepository->findUpcomingSessions();
            return $this->render('session/index.html.twig', [
                'sessionsPassees' => $sessionsPassees,
                'sessionsEnCours' => $sessionsEnCours,
                'sessionsAVenir' => $sessionsAVenir,         
            ]);
        }

    /* FORMULAIRE + EDIT */
        // ROUTES : Cette méthode sera appelée lorsqu’un utilisateur accède à /session/new ou /session/{id}/edit
        #[Route('/session/new', name: 'new_session')] // Création d'un nouvelle session 
        #[Route('/session/{id}/edit', name: 'edit_session')] // Modification d'une nouvelle sessions
        public function new(Request $request, EntityManagerInterface $entityManager, Session $session = null): Response
        {
            // Si aucun objet Session n’est fourni (cas du /new), on en crée un vide
            if (!$session) {
                $session = new Session(); // création d'un objet vide à remplir
            }

            // On génère le formulaire à partir de la classe SessionType
            // On le lie directement à l’objet $session (création ou modification)
            $form = $this->createForm(SessionType::class, $session); // Génère un formulaire basé sur une classe

            // On relie le formulaire aux données envoyées dans la requête HTTP
            // Cela permet de détecter s’il a été soumis et de remplir automatiquement l’objet
            $form->handleRequest($request); // Lie les données reçues à l’objet

            // Vérification : si le formulaire a été soumis ET que tous les champs sont valides
            if ($form->isSubmitted() && $form->isValid()) {

                // On récupère les données du formulaire (remplissent automatiquement $session)
                $session = $form->getData();

                if ($session->estComplet()) {
                    $this->addFlash("error", "Impossible d'ajouter des stagiaires : la session est complète.");
                    return $this->redirectToRoute('app_session');
                }

                // On prépare l’objet pour l’insérer (ou mettre à jour) en base de données
                $entityManager->persist($session); // Prepare PDO

                // On exécute l’insertion ou mise à jour dans la base de données
                $entityManager->flush(); // Execute PDO

                // Une fois terminé, on redirige vers la liste des sessions
                return $this->redirectToRoute('app_session');
            }

            // Si le formulaire n’a pas été soumis ou est invalide, on réaffiche la page avec le formulaire
            return $this->render('session/new.html.twig', [
                'formAddSession' => $form,
                'edit' => $session->getId() // Permet d’afficher dynamiquement “Ajouter” ou “Modifier”
            ]);
        }

    /* SUPPRESSION  */

        // ROUTE :
        #[Route('/session/{id}/delete', name: 'delete_session')]
        public function delete(Session $session, EntityManagerInterface $entityManager): Response
        {
            // On supprime la session de la base de données
            $entityManager->remove($session); // Prépare la suppression
            $entityManager->flush(); // Exécute la suppression

            // Redirection vers la liste des sessions
            return $this->redirectToRoute('app_session');
        }


    /* DETAIL  */
        #[Route('/session/{id}', name: 'show_session')]
        public function show(Session $session = null, SessionRepository $sr): Response
        {
            $nonInscrits = $sr->findNonInscrits($session->getId());
            $nonProgrammes = $sr->findNonProgrammes($session->getId());

            return $this->render('session/show.html.twig', [
            'session' => $session,
            'nonInscrits'=> $nonInscrits,
            'nonProgrammes' => $nonProgrammes,
            ]);
        }
  

    /* INSCRIPTION d’un stagiaire à une session */
        #[Route('/session/{idSession}/{idStagiaire}/add', name: 'add_stagiaire_session')]
        public function addStagiaireToSession( EntityManagerInterface $entityManager, $idSession, $idStagiaire,
        SessionRepository $SessionRepository, StagiaireRepository $StagiaireRepository ): Response {
            // 1) On récupère la session et le stagiaire en base
            $session   = $SessionRepository->find($idSession);
            $stagiaire = $StagiaireRepository->find($idStagiaire);

            if ($session && $stagiaire) {
                // 2) On vérifie si la session est complète
                if ($session->estComplet()) {
                    $this->addFlash('error', "Impossible d'ajouter ce stagiaire : la session est complète.");
                } else {
                    // 3) On rattache le stagiaire à la session
                    $session->addStagiaire($stagiaire);

                    // 4) On incrémente le nombre de places réservées
                    $session->setPlaceReservees(
                        $session->getPlaceReservees() + 1
                    );

                    // 5) On recalcule le nombre de places restantes
                    $session->setPlaceRestantes(
                        $session->getPlaceDisponibles() - $session->getPlaceReservees()
                    );

                    // 6) On enregistre les modifications en base
                    $entityManager->persist($session);
                    $entityManager->flush();

                    $this->addFlash('success', "Le stagiaire a bien été inscrit à la session.");
                }

                // 7) Redirection vers le détail de la session
                return $this->redirectToRoute('show_session', ['id' => $idSession]);
            }

            // session ou stagiaire introuvable : retour à la liste
            return $this->redirectToRoute('app_session');
        }


    /* DÉSINSCRIPTION d’un stagiaire à une session */
        #[Route('/session/{idSession}/{idStagiaire}/remove', name: 'remove_stagiaire_session')]
        public function removeStagiaireFromSession( EntityManagerInterface $entityManager, $idSession, $idStagiaire,
        SessionRepository $SessionRepository, StagiaireRepository $StagiaireRepository ): Response {
           
            // 1) On récupère la session et le stagiaire en base
            $session   = $SessionRepository->find($idSession);
            $stagiaire = $StagiaireRepository->find($idStagiaire);

            if ($session && $stagiaire) {
                // 2) On détache le stagiaire de la session
                $session->removeStagiaire($stagiaire);

                // 3) On décrémente le nombre de places réservées
                $session->setPlaceReservees(
                    $session->getPlaceReservees() - 1
                );

                // 4) On recalcule le nombre de places restantes
                $session->setPlaceRestantes(
                    $session->getPlaceDisponibles() - $session->getPlaceReservees()
                );

                // 5) On enregistre les modifications en base
                $entityManager->persist($session);
                $entityManager->flush();

                $this->addFlash('success', "Le stagiaire a bien été désinscrit de la session.");
            }

            // Redirection vers le détail de la session
            return $this->redirectToRoute('show_session', ['id' => $idSession]);
        }



}




