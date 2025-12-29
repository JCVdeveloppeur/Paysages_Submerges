<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Like;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Repository\LikeRepository;
use App\Repository\EspeceRepository;
use App\Repository\PlanteRepository;
use App\Repository\MaladiePoissonRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\PageBlockRepository;



class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        // Redirige simplement vers le dashboard (ou une vue personnalisée si souhaité plus tard)
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(
    ArticleRepository $articleRepository,
    UserRepository $userRepository,
    CommentaireRepository $commentaireRepository,
    LikeRepository $likeRepository,
    EspeceRepository $especeRepository,
    PageBlockRepository $pageBlockRepository,
    MaladiePoissonRepository $maladiePoissonRepository,
    PlanteRepository $planteRepository
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
    $dernieresMaladies = $maladiePoissonRepository->findBy([], ['id' => 'DESC'], 5);


    return $this->render('admin/dashboard.html.twig', [
        'nombreArticles' => $articleRepository->count([]),
        'articlesEnAttente' => $articleRepository->count(['estApprouve' => false]),
        'nombreUtilisateurs' => $userRepository->count([]),
        'utilisateursActifs' => $userRepository->countUsersActifsDerniersJours(7),
        'utilisateursInactifs' => $userRepository->countUsersInactifsDerniersJours(7),
        'nombreCommentaires' => $commentaireRepository->count([]),
        'nombreLikes' => $likeRepository->count([]),
        'nombreEspeces' => $especeRepository->count([]),
        'nombreMaladies'  => $maladiePoissonRepository->count([]),
        'commentairesAujourdHui' => $commentaireRepository->countCommentairesDepuis($aujourdHui),
        'likesAujourdHui' => $likeRepository->countLikesDepuis($aujourdHui),
        'commentaires7Jours' => $commentaireRepository->countCommentairesDerniersJours(7),
        'likes7Jours' => $likeRepository->countLikesDerniersJours(7),
        'jours' => $labels,
        'commentairesParJour' => $commentairesParJour,
        'likesParJour' => $likesParJour,
        'nombreBlocs' => $pageBlockRepository->count([]),
        'dernieresMaladies' => $dernieresMaladies,
        'nombrePlantes' => $planteRepository->count([]),

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

    #[Route('/admin/article/{id}/approve', name: 'admin_article_approve', methods: ['POST'],requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function approveArticle(Article $article, EntityManagerInterface $em): Response
    {
        $article->setEstApprouve(true);
        $em->flush();

        $this->addFlash('success', 'Article approuvé avec succès.');
        return $this->redirectToRoute('admin_articles_moderation');
    }

    #[Route('/admin/article/{id}/delete', name: 'admin_article_delete', methods: ['POST'],  requirements: ['id' => '\d+'])]
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
    #[Route('/admin/article/{id}', name: 'admin_article_show', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showUnpublishedArticle(Article $article): Response
    {
        return $this->render('admin/articles/show_unpublished.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/admin/utilisateurs', name: 'admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function usersList(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
    $pseudo = $request->query->get('pseudo');
    $role = $request->query->get('role');
    $limit = $request->query->getInt('limit', 5);
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
        $qb->getQuery(),
        $request->query->getInt('page', 1),
        $limit
    );

    return $this->render('admin/users/index.html.twig', [
        'users' => $pagination,
        'sevenDaysAgo' => $sevenDaysAgo,
        'limit' => $limit,
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
    #[Route('/admin/utilisateur/{id}/supprimer', name: 'admin_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
    $em->remove($user);
    $em->flush();

    $this->addFlash('danger', 'Utilisateur supprimé avec succès.');

    return $this->redirectToRoute('admin_users');
    }
    #[Route('/admin/especes', name: 'admin_especes')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminEspeces(EspeceRepository $especeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $especeRepository->createQueryBuilder('e')
            ->orderBy('e.nomCommun', 'ASC')
            ->getQuery();

        $limit = $request->query->getInt('limit', 5);
        if (!in_array($limit, [5, 10, 20], true)) {
            $limit = 5;
        }

        $especes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('admin/espece/admin.html.twig', [
            'especes' => $especes,
            'nombreEspeces' => $especeRepository->count([]),
            'limit' => $limit,
        ]);
    }
    #[Route('/admin/plantes', name: 'admin_plantes')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminPlantes(
        PlanteRepository $planteRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $query = $planteRepository->createQueryBuilder('p')
            ->orderBy('p.nomCommun', 'ASC')
            ->getQuery();

        $limit = $request->query->getInt('limit', 5);
        if (!in_array($limit, [5, 10, 20], true)) {
            $limit = 5;
        }

        $plantes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('admin/plante/admin.html.twig', [
            'plantes' => $plantes,
            'nombrePlantes' => $planteRepository->count([]),
            'limit' => $limit,
        ]);
    }


    #[Route('/admin/maladies', name: 'admin_maladies')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminMaladies(
        MaladiePoissonRepository $repo,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $query = $repo->createQueryBuilder('m')
            ->orderBy('m.nom', 'ASC')
            ->getQuery();

        $limit = $request->query->getInt('limit', 5);
        if (!in_array($limit, [5, 10, 20], true)) {
            $limit = 5;
        }

        $maladies = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('admin/maladie/admin.html.twig', [
            'maladies' => $maladies,
            'nombreMaladies' => $repo->count([]),
            'limit' => $limit,
        ]);
    }
}



