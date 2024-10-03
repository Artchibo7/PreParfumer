<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted("ROLE_EDITOR")]
#[Route('/utilisateur')]
final class UtilisateurController extends AbstractController {
    #[Route(name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    #[Route('/utilisateur/{id}/to/editor', name: 'app_utilisateur_to_editor', methods: ['GET'])]
    public function changeRoles(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response {
        $utilisateur->setRoles(['ROLE_EDITOR', 'ROLE_USER']);
        $entityManager->flush();

        $this->addFlash('success', 'Votre rôle a été ajouté.');

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/utilisateur/{id}/remove/editor/role', name: 'app_utilisateur_remove_editor/role', methods: ['GET'])]
    public function removeRoles(Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response {
        $utilisateur->setRoles(['']);
        $entityManager->flush();

        $this->addFlash('danger', 'Votre rôle a été retiré.');

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe avant de sauvegarder l'utilisateur
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($hashedPassword);

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'Votre utilisateur a été créé.');

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le mot de passe a été modifié, le hacher avant de sauvegarder
            if ($utilisateur->getPassword()) {
                $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
                $utilisateur->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre utilisateur a été modifié.');

            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            $this->addFlash('danger', 'Votre utilisateur a été supprimé.');
        }

        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }


    // #[IsGranted('ROLE_USER')]
    // #[Route('/compte', name: 'app_compte_index', methods: ['GET', 'POST'])]
    // public function compte(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, CommandeRepository $commandeRepository): Response
    // {
    //     /** @var Utilisateur $utilisateur */
    //     $utilisateur = $this->getUser(); // Récupère l'utilisateur connecté
        
    //     $form = $this->createForm(UtilisateurType::class, $utilisateur);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Si le mot de passe a été modifié, le hacher
    //         if ($utilisateur->getPassword()) {
    //             $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
    //             $utilisateur->setPassword($hashedPassword);
    //         }

    //         $entityManager->flush();

    //         $this->addFlash('success', 'Vos informations ont été mises à jour.');

    //         return $this->redirectToRoute('app_compte_index');
    //     }

    //     // Récupère les commandes de l'utilisateur
    //     $commandes = $commandeRepository->findBy(['utilisateur' => $utilisateur]);

    //     return $this->render('compte/index.html.twig', [
    //         'form' => $form->createView(),
    //         'commandes' => $commandes,
    //     ]);
    // }
}
