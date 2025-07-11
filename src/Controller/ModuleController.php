<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Categorie;
use App\Entity\Programme;
use App\Repository\ModuleRepository;
use App\Repository\CategorieRepository;
use App\Repository\ProgrammeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/* MODULE  */

    final class ModuleController extends AbstractController
    {
        /* Liste */
            #[Route('/module', name: 'app_module')]
            public function index(ModuleRepository $moduleRepository): Response
            {
                $modules = $moduleRepository->findAll();
                return $this->render('module/index.html.twig', [
                    'modules' => $modules,
                ]);
            }

        /* détails */
                #[Route('/module/{id}', name: 'show_module')]
            public function show(Module $module): Response
            {
                return $this->render('module/show.html.twig', [
                'module' => $module,
                ]);
            }

    }

/* PROGRAMME A FINIR */

    final class ProgrammeController extends AbstractController
    {
        /* Liste */
            #[Route('/programme', name: 'app_programme')]
            public function index(ProgrammeRepository $programmeRepository): Response
            {
                $programmes = $programmeRepository->findAll();
                return $this->render('programme/index.html.twig', [
                    'programmes' => $programmes,
                ]);
            }

        /* détails */
                #[Route('/programme/{id}', name: 'show_programme')]
            public function show(Programme $programme): Response
            {
                return $this->render('programme/show.html.twig', [
                'programme' => $programme,
                ]);
            }

    }


/* CATEGORIE */

    final class CategorieController extends AbstractController
    {
        /* Liste */
            #[Route('/categorie', name: 'app_categorie')]
            public function index(CategorieRepository $categorieRepository): Response
            {
                $categories = $categorieRepository->findAll();
                return $this->render('categorie/index.html.twig', [
                    'categories' => $categories,
                ]);
            }

        /* Détails */
                #[Route('/categorie/{id}', name: 'show_categorie')]
            public function show(Categorie $categorie): Response
            {
                return $this->render('categorie/show.html.twig', [
                'categorie' => $categorie,
                ]);
            }

    }







