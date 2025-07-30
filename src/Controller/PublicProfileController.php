<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicProfileController extends AbstractController
{
    #[Route('/auteur/{pseudo}', name: 'app_public_profile')]
    public function publicProfile(UserRepository $userRepo, string $pseudo): Response
    {
        $user = $userRepo->findOneBy(['pseudo' => $pseudo]);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // 🔒 Si tu veux un jour gérer des comptes bannis :
        // if (method_exists($user, 'isBanned') && $user->isBanned()) {
        //     throw $this->createAccessDeniedException('Cet utilisateur est banni.');
        // }

        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
        ]);
    }
}

