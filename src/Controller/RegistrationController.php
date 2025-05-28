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
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
        
            // Copie l'email comme identifiant technique
            $user->setUsername($user->getEmail());
        
            // Initialise la date d'inscription
            $user->setDateInscription(new \DateTime());
        
            // Hash du mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
        
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', '🎉 Bienvenue sur Paysages Submergés, ' . $user->getEmail() . ' !');
        
            $security->login($user, LoginFormAuthenticator::class, 'main');
            return $this->redirectToRoute('app_profile');            
            
        }               

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
