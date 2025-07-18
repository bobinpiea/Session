<?php

// CONTRÔLEUR STAGIAIRE (StagiaireController)

// Ce fichier regroupe toutes les actions possibles concernant les stagiaires :
// - afficher la liste des stagiaires
// - créer un nouveau stagiaire
// - afficher le détail d’un stagiaire
//
// Chaque méthode de ce contrôleur correspond à une route (URL) du site.
// Symfony exécute ces méthodes quand on visite les pages du site liées aux stagiaires.
//
// Ce contrôleur utilise :
// - l’entité Stagiaire (pour les données)
// - le formulaire StagiaireType (pour les ajouts/modifications)
// - le repository StagiaireRepository (pour interagir avec la base)

// ------> D'autres fonctions sont à venir également 



namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

   
    #[Route('/stagiaire/new', name: 'new_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    public function new(Request $request, EntityManagerInterface $entityManager, Stagiaire $stagiaire = null): Response
    {
        if (!$stagiaire) {
            $stagiaire = new Stagiaire(); // On crée un nouvel objet Stagiaire s'il n'existe pas 
        }

        $form = $this->createForm(StagiaireType::class, $stagiaire); // On génère le formulaire lié à Stagiaire

        $form->handleRequest($request);

        // Vérification : si le formulaire a été soumis ET que tous les champs sont valides
        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les données du formulaire (remplissent automatiquement $stagiaire)
            $stagiaire = $form->getData();

            // On prépare l’objet pour l’insérer (ou mettre à jour) en base de données
            $entityManager->persist($stagiaire); // Prepare PDO

            // On exécute l’insertion ou mise à jour dans la base de données
            $entityManager->flush(); // Execute PDO

            // Une fois terminé, on redirige vers la liste des stagiaires
            return $this->redirectToRoute('app_stagiaire');
        }

        return $this->render('stagiaire/new.html.twig', [
            'formAddStagiaire' => $form,
            'edit' => $stagiaire->getId() // Ne pas OUBLIER CELA
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
