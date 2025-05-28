<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/mon-profil', name: 'app_profile')]
    public function profile(ArticleRepository $articleRepository): Response
    {
        
    /** @var \App\Entity\User $user */
    $user = $this->getUser();


    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $totalLikes = 0;

    foreach ($user->getArticles() as $article) {
        $totalLikes += $article->getLikes()->count();
    }

    return $this->render('user/profile.html.twig', [
        'user' => $user,
        'totalLikes' => $totalLikes,
    ]);
}


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            // Date d'inscription
            $user->setDateInscription(new \DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'form' => $form,
            'user' => $user,
            'is_edit' => false,
        ]);
        
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Re-hash uniquement si le mot de passe a changé (simplement illustratif ici)
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form,
            'user' => $user,
            'is_edit' => true,
        ]);
                
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
    Request $request,
    User $user,
    EntityManagerInterface $entityManager,
    TokenStorageInterface $tokenStorage,
    SessionInterface $session
        ): Response {
    if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
        // Suppression de l'utilisateur
        $entityManager->remove($user);
        $entityManager->flush();

        // Déconnexion manuelle
        $tokenStorage->setToken(null);
        $session->invalidate();

        // Message dramatique 😭
        $this->addFlash('success', '💔 Votre compte a été supprimé. Merci d\'avoir navigué avec nous. Bon vent marin… 🌊');
    }

    return $this->redirectToRoute('app_accueil');
}

}