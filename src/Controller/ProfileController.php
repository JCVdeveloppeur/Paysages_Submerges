<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;

class ProfileController extends AbstractController
{
    #[Route('/mon-profil', name: 'app_profile')]
#[IsGranted('ROLE_USER')]
public function index(ArticleRepository $articleRepository, CommentaireRepository $commentaireRepository): Response
{
    $user = $this->getUser();

    $articles = $articleRepository->findBy(['user' => $user]);
    $commentaires = $commentaireRepository->findBy(['auteur' => $user]);

    // ✅ Calcul du total de likes reçus
    $totalLikes = 0;
    foreach ($articles as $article) {
        $totalLikes += count($article->getLikes());
    }

    return $this->render('user/profile.html.twig', [
        'user' => $user,
        'articles' => $articles,
        'commentaires' => $commentaires,
        'totalLikes' => $totalLikes,
    ]);
}

}