<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('security/register.html.twig');
    }
}
