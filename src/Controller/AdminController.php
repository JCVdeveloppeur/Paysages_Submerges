<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Like;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(
        ArticleRepository $articleRepository,
        UserRepository $userRepository,
        CommentaireRepository $commentaireRepository,
        LikeRepository $likeRepository
    ): Response {
        $aujourdHui = new \DateTimeImmutable('today');
        $labels = [];
        $commentairesParJour = [];
        $likesParJour = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = (new \DateTimeImmutable('today'))->modify("-{$i} days");
            $labels[] = $date->format('d/m');
            $commentairesParJour[] = $commentaireRepository->countCommentairesDepuis($date->setTime(0, 0));
            $likesParJour[] = $likeRepository->countLikesDepuis($date->setTime(0, 0));
        }

        return $this->render('admin/dashboard.html.twig', [
            'nombreArticles' => $articleRepository->count([]),
            'articlesEnAttente' => $articleRepository->count(['estApprouve' => false]),
            'nombreUtilisateurs' => $userRepository->count([]),
            'nombreCommentaires' => $commentaireRepository->count([]),
            'nombreLikes' => $likeRepository->count([]),
            'commentairesAujourdHui' => $commentaireRepository->countCommentairesDepuis($aujourdHui),
            'likesAujourdHui' => $likeRepository->countLikesDepuis($aujourdHui),
            'commentaires7Jours' => $commentaireRepository->countCommentairesDerniersJours(7),
            'likes7Jours' => $likeRepository->countLikesDerniersJours(7),
            'jours' => $labels,
            'commentairesParJour' => $commentairesParJour,
            'likesParJour' => $likesParJour,
        ]);
    }

    #[Route('/admin/articles/moderation', name: 'admin_articles_moderation')]
    #[IsGranted('ROLE_ADMIN')]
    public function moderation(ArticleRepository $articleRepository): Response
    {
        $articlesEnAttente = $articleRepository->findBy(['estApprouve' => false]);

        return $this->render('admin/articles/moderation.html.twig', [
            'articles' => $articlesEnAttente,
        ]);
    }

    #[Route('/admin/article/{id}/approve', name: 'admin_article_approve', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approveArticle(Article $article, EntityManagerInterface $em): Response
    {
        $article->setEstApprouve(true);
        $em->flush();

        $this->addFlash('success', 'Article approuvé avec succès.');
        return $this->redirectToRoute('admin_articles_moderation');
    }

    #[Route('/admin/article/{id}/delete', name: 'admin_article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteArticle(Article $article, EntityManagerInterface $em): Response
    {
        $em->remove($article);
        $em->flush();

        $this->addFlash('danger', 'Article supprimé.');
        return $this->redirectToRoute('admin_articles_moderation');
    }

    #[Route('/admin/articles', name: 'admin_articles_all')]
    #[IsGranted('ROLE_ADMIN')]
    public function allArticles(
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $query = $articleRepository->createQueryBuilder('a')
            ->orderBy('a.dateCreation', 'DESC')
            ->getQuery();

        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('admin/articles/all_articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/admin/utilisateurs', name: 'admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function usersList(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
    $pseudo = $request->query->get('pseudo');
    $role = $request->query->get('role');
    $sevenDaysAgo = (new \DateTimeImmutable())->modify('-7 days');

    $qb = $userRepository->createQueryBuilder('u');

    if ($pseudo) {
        $qb->andWhere('u.pseudo LIKE :search')
           ->setParameter('search', '%' . $pseudo . '%');
    }

    if ($role) {
        if ($role === 'ROLE_USER') {
            $qb->andWhere('u.roles NOT LIKE :adminRole AND u.roles LIKE :userRole')
               ->setParameter('adminRole', '%ROLE_ADMIN%')
               ->setParameter('userRole', '%ROLE_USER%');
        } else {
            $qb->andWhere('u.roles LIKE :role')
               ->setParameter('role', '%' . $role . '%');
        }
    }

    $qb->orderBy('u.pseudo', 'ASC');

    $pagination = $paginator->paginate(
        $qb,
        $request->query->getInt('page', 1),
        10
    );

    return $this->render('admin/users/index.html.twig', [
        'users' => $pagination,
        'sevenDaysAgo' => $sevenDaysAgo,
    ]);
    }

    #[Route('/admin/interactions', name: 'admin_interactions')]
    #[IsGranted('ROLE_ADMIN')]
    public function interactions(
        CommentaireRepository $commentaireRepository,
        LikeRepository $likeRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $commentairesQuery = $commentaireRepository->createQueryBuilder('c')
            ->orderBy('c.dateCommentaire', 'DESC')
            ->getQuery();

        $likesQuery = $likeRepository->createQueryBuilder('l')
            ->orderBy('l.dateLike', 'DESC')
            ->getQuery();

        $commentaires = $paginator->paginate(
            $commentairesQuery,
            $request->query->getInt('pageCommentaires', 1),
            5,
            ['pageParameterName' => 'pageCommentaires']
        );

        $likes = $paginator->paginate(
            $likesQuery,
            $request->query->getInt('pageLikes', 1),
            5,
            ['pageParameterName' => 'pageLikes']
        );

        $totalCommentaires = $commentaireRepository->count([]);
        $totalLikes = $likeRepository->count([]);

        return $this->render('admin/interactions/index.html.twig', [
            'commentaires' => $commentaires,
            'likes' => $likes,
            'totalCommentaires' => $totalCommentaires,
            'totalLikes' => $totalLikes,
        ]);
    }

    #[Route('/admin/commentaires', name: 'admin_comments_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function manageComments(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        return $this->render('admin/interactions/comments.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/admin/commentaire/{id}/supprimer', name: 'admin_comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteComment(Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $em->remove($commentaire);
        $em->flush();

        $this->addFlash('danger', 'Commentaire supprimé avec succès.');
        return $this->redirectToRoute('admin_interactions');
    }

    #[Route('/admin/like/{id}/supprimer', name: 'admin_like_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteLike(Like $like, EntityManagerInterface $em): Response
    {
        $em->remove($like);
        $em->flush();

        $this->addFlash('danger', 'Like supprimé avec succès.');
        return $this->redirectToRoute('admin_interactions');
    }
    
}



