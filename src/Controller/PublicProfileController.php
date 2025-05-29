<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PublicProfileController extends AbstractController
{
    #[Route('/user/utilisateur/{pseudo}', name: 'app_public_profile')]
    public function show(string $pseudo, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['pseudo' => $pseudo]);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur est introuvable.');
        }

        // Exemple de sécurité personnalisée (si jamais tu ajoutes cette propriété)
        if (method_exists($user, 'isBanned') && $user->isBanned()) {
            throw $this->createAccessDeniedException('Cet utilisateur est banni.');
        }

        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
        ]);
    }
}

