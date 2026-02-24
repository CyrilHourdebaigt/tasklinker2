<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class EmployeController extends AbstractController
{
    // J’affiche la liste de tous les employés.
    #[Route('/employes', name: 'employe_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        // Je récupère tous les employés stockés en BDD
        $employes = $em->getRepository(Employe::class)->findAll();

        // J’envoie la liste des employés à la vue Twig
        return $this->render('employe/index.html.twig', [
            'employes' => $employes,
        ]);
    }

    // Je modifie un employé existant
    #[Route('/employes/{id}/edit', name: 'employe_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Employe $employe, Request $request, EntityManagerInterface $em): Response
    {
        // Je crée le formulaire lié à l’employé sélectionné
        $form = $this->createForm(EmployeType::class, $employe);

        // Je set le rôle de l'utilisateur
        $form->get('role')->setData(
            in_array('ROLE_ADMIN', $employe->getRoles(), true) ? 'ROLE_ADMIN' : 'ROLE_USER'
        );

        $form->handleRequest($request);

        // Si le formulaire est envoyé et que les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {

            $selectedRole = $form->get('role')->getData();

            if ($selectedRole === 'ROLE_ADMIN') {
                $employe->setRoles(['ROLE_ADMIN']);
            } else {
                $employe->setRoles(['ROLE_USER']);
            }

            // Je sauvegarde les modifications en BDD
            $em->flush();

            // Redirection vers la liste des employés
            return $this->redirectToRoute('employe_index');
        }

        // Sinon j'affiche la page d'édition
        return $this->render('employe/edit.html.twig', [
            'employe' => $employe,
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'un employé
    #[Route('/employes/{id}/delete', name: 'employe_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Employe $employe, Request $request, EntityManagerInterface $em): Response
    {
        // J'utilise isCsrfTokenValid en sécurité pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete_employe_' . $employe->getId(), $request->request->get('_token'))) {
            $em->remove($employe);
            $em->flush();
        }

        // Redirection vers la liste des employés
        return $this->redirectToRoute('employe_index');
    }
}
