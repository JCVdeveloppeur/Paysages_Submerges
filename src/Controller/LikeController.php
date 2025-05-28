<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Like;
use App\Form\LikeType;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/likes')]
class LikeController extends AbstractController
{
    #[Route('/', name: 'like_index', methods: ['GET'])]
    public function index(LikeRepository $likeRepository): Response
    {
        return $this->render('like/index.html.twig', [
            'likes' => $likeRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'like_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $like = new Like();
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($like);
            $em->flush();

            $this->addFlash('success', 'Like ajouté avec succès.');
            return $this->redirectToRoute('like_index');
        }

        return $this->render('like/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'like_show', methods: ['GET'])]
    public function show(Like $like): Response
    {
        return $this->render('like/show.html.twig', [
            'like' => $like,
        ]);
    }

    #[Route('/{id}/modifier', name: 'like_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Like $like, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LikeType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Like modifié avec succès.');
            return $this->redirectToRoute('like_index');
        }

        return $this->render('like/edit.html.twig', [
            'form' => $form->createView(),
            'like' => $like,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'like_delete', methods: ['POST'])]
    public function delete(Request $request, Like $like, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$like->getId(), $request->request->get('_token'))) {
            $em->remove($like);
            $em->flush();
            $this->addFlash('success', 'Like supprimé avec succès.');
        }

        return $this->redirectToRoute('like_index');
    }

    #[Route('/toggle/{id}', name: 'app_like_toggle', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function toggle(Request $request, Article $article, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        $existingLike = $em->getRepository(Like::class)->findOneBy([
            'user' => $user,
            'article' => $article,
        ]);

        if ($existingLike) {
            $em->remove($existingLike);
        } else {
            $like = new Like();
            $like->setUser($user);
            $like->setDateLike(new \DateTime());
            $like->setArticle($article);
            $em->persist($like);
        }

        $em->flush();

        // Si AJAX (XMLHttpRequest), retourne un JSON avec le nouveau total
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'likeCount' => count($article->getLikes()),
                'liked' => $existingLike ? false : true
            ]);
        }

        // Sinon redirection classique (fallback)
        return $this->redirectToRoute('article_show', [
            'id' => $article->getId(),
        ]);
    }
}

