<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
    {
    #[Route('/articles', name: 'app_articles')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
{
    $searchTerm = $request->query->get('q');
    $auteurId = $request->query->get('auteur');

    if ($auteurId) {
        $articles = $articleRepository->findBy(
            ['user' => $auteurId],
            ['createdAt' => 'DESC']
        );
    } elseif ($searchTerm) {
        $articles = $articleRepository->createQueryBuilder('a')
            ->where('a.titre LIKE :searchTerm OR a.contenu LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    } else {
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);
    }

    $badgeClasses = [
        'Biotope Amérique du sud' => 'badge-amerique-sud',
        'Biotope asiatique' => 'badge-asiatique',
        'Biotope africain' => 'badge-africain',
        'Biotope australien' => 'badge-australien',
        'Autre' => 'badge-autre',
    ];

    return $this->render('article/index.html.twig', [
        'articles' => $articles,
        'badgeClasses' => $badgeClasses,
        'searchTerm' => $searchTerm,
    ]);
    }


    #[Route('/articles/test-badges', name: 'article_test_badges')]
    public function testBadges(): Response
    {
    $badgeClasses = [
        'Biotope Amérique du sud' => 'badge-amerique-sud',
        'Biotope asiatique' => 'badge-asiatique',
        'Biotope africain' => 'badge-africain',
        'Biotope australien' => 'badge-australien',
        'Autre' => 'badge-autre',
    ];
    
    return $this->render('article/test_badges.html.twig', [
        'badgeClasses' => $badgeClasses,
    ]);
    }

    #[Route('/articles/test', name: 'article_test')]
    public function test(EntityManagerInterface $em): Response
    {
        $article = new Article();
        $article->setTitre('Bienvenue dans l\'aquarium');
        $article->setContenu('Découvrez comment recréer un biotope asiatique avec plantes naturelles et poissons colorés.');
        $article->setCategorie('Biotope asiatique');
        $article->setStatut('Publié');
        $article->setCreatedAt(new \DateTime());
        $article->setDateCreation(new \DateTime());
        $article->setDatePublication(new \DateTime());
        $article->setImage('test.jpg'); // Ce fichier doit exister dans public/uploads/articles/test.jpg

        $user = $em->getRepository(User::class)->find(1);
        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur avec l\'ID 1');
        }
        $article->setUser($user);

        $em->persist($article);
        $em->flush();

        return new Response('Article de test ajouté !');
    }

    #[Route('/articles/creer', name: 'article_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
    // ✅ Vérifie manuellement si l'utilisateur est connecté
    if (!$this->getUser()) {
        $this->addFlash('info', 'Connecte-toi pour rédiger un article ! 🖊️');
        return $this->redirectToRoute('app_login');
    }

    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $article->setCreatedAt(new \DateTime());
        $article->setDateCreation(new \DateTime());
        $article->setUser($this->getUser());

        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/articles',
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', "Erreur lors de l'upload de l'image.");
            }

            $article->setImage($newFilename);
        }

        $em->persist($article);
        $em->flush();

        $this->addFlash('success', 'Article créé avec succès !');
        return $this->redirectToRoute('app_articles');
        }

    return $this->render('article/create.html.twig', [
        'form' => $form->createView(),
    ]);
    }


    #[Route('/articles/{id}', name: 'article_show', requirements: ['id' => '\d+'])]
    public function show(Article $article, CommentaireRepository $commentaireRepository, ArticleRepository $articleRepository): Response
    {
    $badgeClasses = [
        'Biotope Amérique du sud' => 'badge-amerique-sud',
        'Biotope asiatique' => 'badge-asiatique',
        'Biotope africain' => 'badge-africain',
        'Biotope australien' => 'badge-australien',
        'Autre' => 'badge-autre',
    ];

    $commentaires = $commentaireRepository->findBy([
        'article' => $article,
        'approuve' => true
    ], ['dateCommentaire' => 'DESC']);

    $isLiked = false;

    if ($this->getUser()) {
        // Attention : on suppose que LikeRepository est injecté dans le contrôleur ou récupéré autrement
        $like = $article->getLikes()->filter(function ($like) {
            return $like->getUser() === $this->getUser();
        })->first();

        $isLiked = $like !== false;
    }

    return $this->render('article/show.html.twig', [
        'article' => $article,
        'badgeClasses' => $badgeClasses,
        'commentaires' => $commentaires,
        'isLiked' => $isLiked,
    ]);
}

    #[Route('/articles/{id}/edit', name: 'article_edit')]
    public function edit(Request $request, Article $article, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $article->setTitre($form->get('titre')->getData());
        $article->setContenu($form->get('contenu')->getData());
        $article->setCategorie($form->get('categorie')->getData());
        $article->setStatut($form->get('statut')->getData());
        $article->setDatePublication($form->get('datePublication')->getData());
        $article->setUser($form->get('user')->getData());
        $article->setUpdatedAt(new \DateTime());
        

        // Traitement de l'image (si une nouvelle image a été uploadée)
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/articles',
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', "Erreur lors de l'upload de l'image.");
            }

            $article->setImage($newFilename);
        }

        // On met à jour les dates si nécessaire (optionnel si modifiable)
        $article->setDatePublication($article->getDatePublication());
        $article->setUser($article->getUser());
        $article->setUpdatedAt(new \DateTime()); // Ajoute cette ligne


        $em->flush();


        $this->addFlash('success', 'Article mis à jour avec succès.');
        return $this->redirectToRoute('app_articles');
    }

    return $this->render('article/edit.html.twig', [
        'form' => $form->createView(),
        'article' => $article,
    ]);
    }
    #[Route('/articles/{id}/supprimer', name: 'article_delete', methods: ['POST'])]
public function delete(Request $request, Article $article, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article supprimé avec succès.');
    }

    return $this->redirectToRoute('app_articles');
}
}

