<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    // Page d'accueil, affiche les projets accessibles par l'utilisateur connecté
    #[Route('/home', name: 'app_home')]
    public function index(ProjetRepository $projetRepository): Response
    {
        $projets = $projetRepository->findAccessibleProjects($this->getUser());

        return $this->render('home/index.html.twig', [
            'projets' => $projets,
        ]);
    }
}
