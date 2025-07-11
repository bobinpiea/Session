<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Repository\StagiaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $stagiaireRepository): Response
    {
        
      //  $sessions = $sessionRepository->findAll();
        $stagiaires = $stagiaireRepository->findAll(); 
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
         //   'sessions' => $sessions, 
        ]);
    }


     #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/show.html.twig', [
        'stagiaire' => $stagiaire,
        ]);
    }
}
