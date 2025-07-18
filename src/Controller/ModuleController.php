<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Entity\Categorie;
use App\Entity\Programme;
use App\Repository\ModuleRepository;
use App\Repository\CategorieRepository;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/* MODULE  */

    final class ModuleController extends AbstractController
    {
        /* LISTES */

            /* Liste MODULE */
                #[Route('/module', name: 'app_module')]
                public function index(ModuleRepository $moduleRepository): Response
                {
                    $modules = $moduleRepository->findAll();
                    return $this->render('module/index.html.twig', [
                        'modules' => $modules,
                    ]);
                }

            /* Liste PROGRAMME */
               #[Route('/programme', name: 'app_programme')]
                public function listProgrammes(ProgrammeRepository $programmeRepository): Response
                {
                    $programmes = $programmeRepository->findAll();
                    return $this->render('programme/index.html.twig', [
                        'programmes' => $programmes,
                    ]);
                }

            /* Liste CATEGORIE */
               #[Route('/categorie', name: 'app_categorie')]
                public function listCategories(CategorieRepository $categorieRepository): Response
                {
                    $categories = $categorieRepository->findAll();
                    return $this->render('categorie/index.html.twig', [
                        'categories' => $categories,
                    ]);
                }
            

        /* FORMULAIRES */  
             
            /* Formulaire MODULE */
                // FORMULAIRE + ÉDITION
                #[Route('/module/new', name: 'new_module')]
                #[Route('/module/{id}/edit', name: 'edit_module')]
                public function new(Request $request, EntityManagerInterface $entityManager, Module $module = null): Response
                {
                    // Si aucun objet Module n’est fourni, on en crée un vide
                    if (!$module) {
                        $module = new Module(); // On crée un nouvel objet Module s'il n'existe pas
                    }

                    // On génère le formulaire à partir de la classe ModuleType
                    // et on le lie directement à l’objet $module (création ou modification)
                    $form = $this->createForm(ModuleType::class, $module); // Génère un formulaire basé sur une classe

                    // On relie le formulaire aux données envoyées dans la requête HTTP
                    $form->handleRequest($request); // Lie les données reçues à l’objet

                    // Vérification : si le formulaire a été soumis ET que tous les champs sont valides
                    if ($form->isSubmitted() && $form->isValid()) {

                        // On récupère les données du formulaire (remplissent automatiquement $module)
                        $module = $form->getData();

                        // On prépare l’objet pour l’insérer (ou mettre à jour) en base de données
                        $entityManager->persist($module); // Prepare PDO

                        // On exécute l’insertion ou mise à jour dans la base de données
                        $entityManager->flush(); // Execute PDO

                        // Une fois terminé, on redirige vers la liste des modules
                        return $this->redirectToRoute('app_module');
                    }

                    // Si le formulaire n’a pas été soumis ou est invalide, on réaffiche la page avec le formulaire
                    return $this->render('module/new.html.twig', [
                        'formAddModule' => $form,
                        'edit' => $module->getId() // A Ne pas/plus oublier
                    ]);
                }
                       
            /* Formulaire PROGRAMME */

            /* Formulaire CATEGORIE */
            
        /* DETAILS */    

            /* détail MODULE */
                    #[Route('/module/{id}', name: 'show_module')]
                public function show(Module $module): Response
                {
                    return $this->render('module/show.html.twig', [
                    'module' => $module,
                    ]);
                }
                        
            /* Détail PROGRAMME  - mais aucun détail à afficher de plus ? */
                #[Route('/programme/{id}', name: 'show_programme')]
                public function showProgramme(Programme $programme): Response
                {
                    return $this->render('programme/show.html.twig', [
                        'programme' => $programme,
                    ]);
                }
            

            /* Détail CATEGORIE  - mais aucun détail à afficher de plus ? */
                #[Route('/categorie/{id}', name: 'show_categorie')]
                public function showCategorie(Categorie $categorie): Response
                {
                    return $this->render('categorie/show.html.twig', [
                        'categorie' => $categorie,
                    ]);
                }


    }
