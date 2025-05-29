<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // Copie l'email comme identifiant si aucun username défini
            if (!$user->getUsername()) {
                $user->setUsername($user->getEmail());
            }

            // Ajout de la date d’inscription
            $user->setDateInscription(new \DateTime());

            // Enregistrement en BDD
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de bienvenue
            $this->addFlash('success', '🎉 Bienvenue sur Paysages Submergés, ' . $user->getPseudo() . ' !');

            // Connexion automatique
            $security->login($user, LoginFormAuthenticator::class, 'main');

            // Redirection vers la page profil
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

