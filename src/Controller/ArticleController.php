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

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $searchTerm = $request->query->get('q');
        $auteurId = $request->query->get('auteur');

        $articles = match (true) {
            $auteurId => $articleRepository->findBy(['user' => $auteurId], ['createdAt' => 'DESC']),
            $searchTerm => $articleRepository->createQueryBuilder('a')
                ->where('a.titre LIKE :term OR a.contenu LIKE :term')
                ->setParameter('term', '%' . $searchTerm . '%')
                ->orderBy('a.createdAt', 'DESC')
                ->getQuery()->getResult(),
            default => $articleRepository->findBy(['estApprouve' => true], ['createdAt' => 'DESC']),
        };

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/article/{id<\d+>}', name: 'app_article_show')]
    public function show(
        Article $article,
        Request $request,
        EntityManagerInterface $em,
        CommentaireRepository $commentaireRepository
    ): Response {
        $commentaires = $commentaireRepository->findBy([
            'article' => $article,
            'approuve' => true
        ], ['dateCommentaire' => 'DESC']);

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
            return $this->redirectToRoute('app_article_show', ['id' => $article->getId()]);
        }

        $isLiked = $this->getUser() && $article->getLikes()->exists(
            fn($key, $like) => $like->getUser() === $this->getUser()
        );

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentaires' => $commentaires,
            'commentaireForm' => $form->createView(),
            'isLiked' => $isLiked,
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
                        $this->addFlash('danger', "Erreur lors de l'upload de l’image ($field).");
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
            throw $this->createAccessDeniedException('Accès refusé. Vous n’êtes pas autorisé à supprimer cet article.');
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








