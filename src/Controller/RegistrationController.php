<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UtilisateurRepository;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Vérifier si l'email existe déjà dans la base de données
        $existingUser = $utilisateurRepository->findOneBy(['email' => $form->get('email')->getData()]);

        if ($existingUser) {
            // Ajouter un message flash d'erreur
            $this->addFlash('danger', 'Cet e-mail est déjà utilisé.');

            // Retourner la vue avec le formulaire
            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form,
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            
            $plainPassword = $form->get('plainPassword')->getData();

            // Encoder le mot de passe en clair
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Votre compte a été créé avec succès.');

            // Rediriger vers la page de connexion ou une autre page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
