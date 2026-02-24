<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProjetController extends AbstractController
{
    // J'affiche la liste des projets accessibles par l'utilisatuer connecté
    #[Route('/projets', name: 'projet_index')]
    public function index(ProjetRepository $projetRepository): Response
    {
        $user = $this->getUser();

        return $this->render('projet/index.html.twig', [
            'projets' => $projetRepository->findAccessibleProjects($user),
        ]);
    }

    // J’affiche le détail d’un projet 
    #[Route('/projets/{id}', name: 'projet_show', requirements: ['id' => '\d+'])]
    public function show(Projet $projet, TacheRepository $tacheRepository): Response
    {
        // J'applique le voter
        $this->denyAccessUnlessGranted('projet.view', $projet);

        // Je récupère les tâches du projet selon leur statut
        $tachesToDo = $tacheRepository->findBy(['projet' => $projet, 'statut' => 'To Do']);
        $tachesDoing = $tacheRepository->findBy(['projet' => $projet, 'statut' => 'Doing']);
        $tachesDone = $tacheRepository->findBy(['projet' => $projet, 'statut' => 'Done']);

        // J’envoie le projet et ses tâches à la vue
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
            'tachesToDo' => $tachesToDo,
            'tachesDoing' => $tachesDoing,
            'tachesDone' => $tachesDone,
        ]);
    }

    // Je modifie un projet existant
    #[Route('/projets/{id}/edit', name: 'projet_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Projet $projet, Request $request, EntityManagerInterface $em): Response
    {
        // Je crée le formulaire lié au projet sélectionné
        $form = $this->createForm(ProjetType::class, $projet);
        // Je récupère les données envoyées par le formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Je sauvegarde les modifications en base de données
            $em->flush();

            // Je redirige vers la page du projet modifié
            return $this->redirectToRoute('projet_show', [
                'id' => $projet->getId(),
            ]);
        }

        // Sinon, j’affiche le formulaire d’édition
        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }


    // Création d'un nouveau projet
    #[Route('/projets/nouveau', name: 'projet_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Je crée un nouveau projet vide
        $projet = new Projet();

        // Je crée le formulaire associé
        $form = $this->createForm(ProjetType::class, $projet);
        // Je récupère les données
        $form->handleRequest($request);

        // J’enregistre le projet en base de données
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($projet);
            $em->flush();

            // Une fois créé, je redirige vers la page du projet
            return $this->redirectToRoute('projet_show', [
                'id' => $projet->getId(),
            ]);
        }

        return $this->render('projet/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Archivage d'un projet
    #[Route('/projets/{id}/archive', name: 'projet_archive', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function archive(Projet $projet, Request $request, EntityManagerInterface $em): Response
    {
        // Je vérifie le token CSRF pour sécuriser l’action
        if ($this->isCsrfTokenValid('archive_projet_' . $projet->getId(), $request->request->get('_token'))) {
            // Je passe le projet en "archivé"
            $projet->setArchive(true);
            // Je sauvegarde le changement en base de données
            $em->flush();
        }

        // Je redirige vers la page d’accueil
        return $this->redirectToRoute('projet_index');
    }
}
