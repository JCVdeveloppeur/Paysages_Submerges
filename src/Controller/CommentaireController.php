<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentaires')]
class CommentaireController extends AbstractController
{
    // 🔐 CRUD classique pour l'administration
    #[Route('/', name: 'commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commentaire);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès.');
            return $this->redirectToRoute('commentaire_index');
        }

        return $this->render('commentaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/modifier', name: 'commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Commentaire modifié avec succès.');
            return $this->redirectToRoute('commentaire_index');
        }

        return $this->render('commentaire/edit.html.twig', [
            'form' => $form->createView(),
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $em->remove($commentaire);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        }

        return $this->redirectToRoute('commentaire_index');
    }

    // ✨ Ajout public de commentaire depuis la page d’un article
    #[Route('/ajouter/{id}', name: 'commentaire_ajouter', methods: ['POST'])]
    public function ajouter(
        Request $request,
        Article $article,
        EntityManagerInterface $em
    ): Response {
        $commentaire = new Commentaire();
        $commentaire->setArticle($article);
        $commentaire->setDateCommentaire(new \DateTime());
        $commentaire->setApprouve(false); // validé par admin

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
        } else {
            $this->addFlash('danger', 'Une erreur est survenue lors de l\'ajout du commentaire.');
        }

        return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
    }
    
    #[Route('/{id}/approuver', name: 'commentaire_approve', methods: ['POST'])]
    public function approve(Commentaire $commentaire, EntityManagerInterface $em, Request $request): Response
    {
    if ($this->isCsrfTokenValid('approve'.$commentaire->getId(), $request->request->get('_token'))) {
        $commentaire->setApprouve(true);
        $em->flush();
        $this->addFlash('success', 'Commentaire approuvé avec succès.');
    }

    return $this->redirectToRoute('admin_interactions');
    }

}

