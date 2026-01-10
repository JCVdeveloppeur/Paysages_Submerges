<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(
        Request $request,
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator
    ): Response {
        $searchTerm = $request->query->get('q');
        $auteurId   = $request->query->get('auteur');

        //  Limite choisie (6/9/12)
        $limit = $request->query->getInt('limit', 6);
        if (!in_array($limit, [6, 9, 12], true)) {
            $limit = 6;
        }

        // Base QueryBuilder
        $qb = $articleRepository->createQueryBuilder('a')
            ->andWhere('a.estApprouve = true')
            ->orderBy('a.createdAt', 'DESC');

        // Filtre par auteur
        if ($auteurId) {
            $qb->andWhere('a.user = :u')
            ->setParameter('u', $auteurId);
        }

        // Recherche texte
        if ($searchTerm) {
            $qb->andWhere('(a.titre LIKE :term OR a.contenu LIKE :term)')
            ->setParameter('term', '%' . $searchTerm . '%');
        }

        // Pagination
        $articles = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $limit
        );

        // Sidebar : derniers articles (non paginés)
        $lastArticles = $articleRepository->findLatestPublished(3);

        return $this->render('article/index.html.twig', [
            'articles'     => $articles,
            'searchTerm'   => $searchTerm,
            'lastArticles' => $lastArticles,
            'limit'        => $limit,
        ]);
    }

    #[Route('/articles/biotope/{biotope}', name: 'app_articles_biotope')]
    public function byBiotope(
        string $biotope,
        Request $request,
        ArticleRepository $articleRepository,
        PaginatorInterface $paginator
    ): Response {

        $returnUrl = $request->query->get('return');
        $returnTitle = $request->query->get('return_title');

        // slug -> label (valeur stockée en DB)
        $map = [
            'amerique-sud'       => 'Biotope Amérique du sud',
            'amerique-centrale'  => 'Biotope Amérique centrale',
            'asiatique'          => 'Biotope asiatique',
            'africain'           => 'Biotope africain',
            'australien'         => 'Biotope australien',
            'europeen'           => 'Biotope européen',
            'eaux-saumatres'     => 'Biotope eaux saumâtres',
            'mangroves'          => 'Biotope mangroves',
            'autre'              => 'Autre',
        ];

        $label = $map[$biotope] ?? null;
        if (!$label) {
            throw $this->createNotFoundException();
        }

        $limit = $request->query->getInt('limit', 6);
        if (!in_array($limit, [6, 9, 12], true)) {
            $limit = 6;
        }

        $qb = $articleRepository->createQueryBuilder('a')
            ->andWhere('a.estApprouve = true')
            ->andWhere('a.categorie = :cat')
            ->setParameter('cat', $label)
            ->orderBy('a.createdAt', 'DESC');

        $articles = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $limit
        );

        $lastArticles = $articleRepository->findLatestPublished(3);

        return $this->render('article/index.html.twig', [
            'articles'        => $articles,
            'lastArticles'    => $lastArticles,
            'limit'           => $limit,
            'returnUrl' => $returnUrl,
            'returnTitle' => $returnTitle,
            'biotope'         => $biotope,

            'currentBiotope'  => $biotope,
            'currentBioLabel' => $label,
            'searchTerm'      => null,
        ]);
    }

    #[Route('/article/{id<\d+>}', name: 'app_article_show')]
    public function show(
        Article $article,
        Request $request,
        EntityManagerInterface $em,
        CommentaireRepository $commentaireRepository,
        ArticleRepository $articleRepository
    ): Response {

        // 🔒 Protection : article non publié
        if (
            !$article->getEstApprouve()
            && !$this->isGranted('ROLE_ADMIN')
            && $article->getUser() !== $this->getUser()
        ) {
            throw $this->createNotFoundException();
        }

        // =============================
        // Commentaires
        // =============================
        $commentaires = $commentaireRepository->findBy(
            ['article' => $article, 'approuve' => true],
            ['dateCommentaire' => 'DESC']
        );

        $commentaire = new Commentaire();
        $commentaire->setArticle($article)
            ->setDateCommentaire(new \DateTime())
            ->setApprouve(false);

        if ($this->getUser()) {
            $commentaire->setAuteur($this->getUser());
        }

        $form = $this->createForm(CommentaireType::class, $commentaire, [
            'is_authenticated' => $this->getUser() !== null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', '💬 Merci pour votre commentaire ! Il sera visible après validation.');

            // IMPORTANT : on conserve le contexte (biotope/q/page/limit) au refresh
            return $this->redirectToRoute('app_article_show', array_filter([
                'id' => $article->getId(),
                'biotope' => $request->query->get('biotope'),
                'q' => $request->query->get('q'),
                'page' => $request->query->get('page'),
                'limit' => $request->query->get('limit'),
            ], fn ($v) => $v !== null && $v !== ''));
        }

        $isLiked = $this->getUser() && $article->getLikes()->exists(
            fn($key, $like) => $like->getUser() === $this->getUser()
        );

        // Blocs “à côté”
        $related = $articleRepository->findRelatedByCategory($article->getCategorie(), $article->getId(), 3);
        $lastArticles = $articleRepository->findLatestPublished(3, $article->getId());

        // Fallback global (optionnel)
        $prev = $articleRepository->findPrevPublished($article->getCreatedAt());
        $next = $articleRepository->findNextPublished($article->getCreatedAt());

        // =============================
        // Mode exploration biotope (contexte)
        // =============================
        $biotope = $request->query->get('biotope'); // slug ex: "asiatique"
        $q = trim((string) $request->query->get('q', ''));
        $page  = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 6);
        if (!in_array($limit, [6, 9, 12], true)) { $limit = 6; }

        // slug -> label (valeur stockée en DB)
        $map = [
            'amerique-sud'       => 'Biotope Amérique du sud',
            'amerique-centrale'  => 'Biotope Amérique centrale',
            'asiatique'          => 'Biotope asiatique',
            'africain'           => 'Biotope africain',
            'australien'         => 'Biotope australien',
            'europeen'           => 'Biotope européen',
            'eaux-saumatres'     => 'Biotope eaux saumâtres',
            'mangroves'          => 'Biotope mangroves',
            'autre'              => 'Autre',
        ];
        $biotopeLabel = $biotope ? ($map[$biotope] ?? null) : null;

        // Back URL : retour à la liste (filtrée ou non)
        $queryParams = array_filter([
            'q' => $q !== '' ? $q : null,
            'page' => $page,
            'limit' => $limit,
        ], fn ($v) => $v !== null);

        if ($biotope && $biotopeLabel) {
            // ✅ biotope est un paramètre de route ici
            $backUrl = $this->generateUrl('app_articles_biotope', ['biotope' => $biotope] + $queryParams);
        } else {
            $backUrl = $this->generateUrl('app_articles', $queryParams);
        }

        // Base query "dans le contexte" (même tri que l’index : createdAt DESC)
        $baseQb = $articleRepository->createQueryBuilder('a')
            ->andWhere('a.estApprouve = true');

        if ($biotopeLabel) {
            $baseQb->andWhere('a.categorie = :cat')->setParameter('cat', $biotopeLabel);
        }
        if ($q !== '') {
            $baseQb->andWhere('(a.titre LIKE :term OR a.contenu LIKE :term)')
                ->setParameter('term', '%' . $q . '%');
        }

        // ⏮ Précédent dans le contexte (plus récent, car index = DESC)
        $prevCtx = (clone $baseQb)
            ->andWhere('a.createdAt > :currentDate')
            ->setParameter('currentDate', $article->getCreatedAt())
            ->orderBy('a.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // ⏭ Suivant dans le contexte (plus ancien)
        $nextCtx = (clone $baseQb)
            ->andWhere('a.createdAt < :currentDate')
            ->setParameter('currentDate', $article->getCreatedAt())
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $prevUrl = $prevCtx ? $this->generateUrl('app_article_show', array_filter([
            'id' => $prevCtx->getId(),
            'biotope' => $biotope,
            'q' => $q !== '' ? $q : null,
            'page' => $page,
            'limit' => $limit,
        ], fn($v) => $v !== null)) : null;

        $nextUrl = $nextCtx ? $this->generateUrl('app_article_show', array_filter([
            'id' => $nextCtx->getId(),
            'biotope' => $biotope,
            'q' => $q !== '' ? $q : null,
            'page' => $page,
            'limit' => $limit,
        ], fn($v) => $v !== null)) : null;

        return $this->render('article/show.html.twig', [
            'article'         => $article,
            'commentaires'    => $commentaires,
            'commentaireForm' => $form->createView(),
            'isLiked'         => $isLiked,
            'related'         => $related,
            'lastArticles'    => $lastArticles,

            // fallback global (si tu veux l’utiliser ailleurs)
            'prev' => $prev,
            'next' => $next,

            // contexte biotope
            'backUrl'  => $backUrl,
            'prevUrl'  => $prevUrl,
            'nextUrl'  => $nextUrl,
            'currentBiotope' => $biotope,
        ]);
    }


    #[Route('/article/conditions', name: 'article_conditions')]
    public function conditions(Request $request): Response
    {
        if ($request->isMethod('POST') && $request->request->get('accept_conditions')) {
            $request->getSession()->set('hasAcceptedConditions', true);
            return $this->redirectToRoute('app_article_new');
        }

        return $this->render('article/conditions.html.twig');
    }

    #[Route('/article/nouveau', name: 'app_article_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, SessionInterface $session): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('info', 'Connecte-toi pour rédiger un article ! 🖊️');
            return $this->redirectToRoute('app_login');
        }

        if (!$session->get('hasAcceptedConditions')) {
            $this->addFlash('warning', 'Merci de lire et accepter les conditions avant de rédiger un article.');
            return $this->redirectToRoute('article_conditions');
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTime());
            $article->setDateCreation(new \DateTime());
            $article->setUser($this->getUser());
            $article->setEstApprouve(false);

            foreach (['image', 'imageGauche', 'imageDroite', 'imageHeader'] as $field) {
                $file = $form->get($field)->getData();
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $slugger->slug($originalFilename) . '-' . uniqid() . '.' . $file->guessExtension();
                    try {
                        $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/articles', $newFilename);
                        $setter = 'set' . ucfirst($field);
                        $article->$setter($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('danger', "Erreur lors de l'upload de l’image ($field).");
                    }
                }
            }

            $em->persist($article);
            $em->flush();

            $session->remove('hasAcceptedConditions');

            $this->addFlash('success', 'Article créé avec succès !');
            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/modifier', name: 'article_edit')]
    public function edit(
        Article $article,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach (['image', 'imageGauche', 'imageDroite', 'imageHeader'] as $field) {
                $file = $form->get($field)->getData();
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $slugger->slug($originalFilename) . '-' . uniqid() . '.' . $file->guessExtension();
                    try {
                        $file->move($this->getParameter('kernel.project_dir') . '/public/uploads/articles', $newFilename);
                        $setter = 'set' . ucfirst($field);
                        $article->$setter($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('danger', "Erreur lors de l'upload de l'image ($field).");
                    }
                }
            }

            $em->flush();

            $this->addFlash('success', 'Article modifié avec succès !');
            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/supprimer', name: 'article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès refusé. Vous n\'êtes pas autorisé à supprimer cet article.');
        }

        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $submittedToken)) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', '🗑️ Article supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide. Suppression annulée.');
        }

        return $this->redirectToRoute('app_articles');
    }
}








