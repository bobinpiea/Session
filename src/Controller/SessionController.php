<?php

namespace App\Controller;

use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(EntityManagerInterface $entityManager): Response
    {   
        // Render : Permet de faire le lien entre le controller et la vue
        $sessions = $entityManager->getRepository(Session::class)->findAll(); 
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions
        ]);
    }
}
