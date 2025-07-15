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

    /* Formulaire  */
        #[Route('/session/new', name: 'new_session')]
        public function new(Request $request): Response
        {
            $session = new Session(); // On crée un nouvel objet Session

            $form = $this->createForm(SessionType::class, $session); // On génère le formulaire lié à Session

            return $this->render('session/new.html.twig', [
                'formAddSession' => $form,
            ]);
        }

    /* Détail  */
        #[Route('/session/{id}', name: 'show_session')]
        public function show(Session $session): Response
        {
            return $this->render('session/show.html.twig', [
            'session' => $session,
            ]);
        }

}
