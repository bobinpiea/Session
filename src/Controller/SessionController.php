<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class SessionController extends AbstractController
{
    /* Liste  */
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

    /* Formulaire + Edit */
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

    /* Suppression  */

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


    /* Détail  */
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



}


