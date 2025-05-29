<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        'nbCommentaires' => count($commentaires), // <-- ici !
    ]);
    }

    #[Route('/mon-compte/mot-de-passe', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    $form = $this->createForm(ChangePasswordFormType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $newPassword = $form->get('plainPassword')->getData();
        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
        $em->flush();

        $this->addFlash('success', 'Mot de passe modifié avec succès !');
        return $this->redirectToRoute('app_profile');
    }

    return $this->render('user/change_password.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/mon-compte/supprimer', name: 'app_delete_account', methods: ['GET', 'POST'])]
    public function deleteAccount(EntityManagerInterface $em, Request $request, Security $security): Response
    {
    $user = $this->getUser();

    if ($this->isCsrfTokenValid('delete-user', $request->get('_token'))) {
    $em->remove($user);
    $em->flush();

    $request->getSession()->invalidate();

    $this->addFlash('success', 'Compte supprimé avec succès !');
    return $this->redirectToRoute('app_home');
    }


    return $this->render('user/delete_confirm.html.twig', [
       
    ]);
}

}