<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ProfileType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/mon-profil', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(
        ArticleRepository $articleRepository,
        CommentaireRepository $commentaireRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ): Response {
        $user = $this->getUser();

        $articles = $articleRepository->findBy(['user' => $user]);
        $commentaires = $commentaireRepository->findBy(['auteur' => $user]);
        $totalLikes = array_reduce($articles, fn($sum, $a) => $sum + count($a->getLikes()), 0);

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        $avatars = [];
            $avatarDir = $this->getParameter('kernel.project_dir') . '/public/images/avatars';
            if (is_dir($avatarDir)) {
                foreach (scandir($avatarDir) as $file) {
                    if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
                        $avatars[] = $file;
                    }
                }
            }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès. Veuillez vous reconnecter.');

            // Redirection AVANT la déconnexion pour éviter erreur avec $user
            $response = $this->redirectToRoute('app_accueil');

            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            return $response;
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'articles' => $articles,
            'commentaires' => $commentaires,
            'totalLikes' => $totalLikes,
            'nbCommentaires' => count($commentaires),
            'form' => $form->createView(),
             'avatars' => $avatars, 
        ]);
    }

    #[Route('/mon-compte/mot-de-passe', name: 'app_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, $user);
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
    #[IsGranted('ROLE_USER')]
    public function deleteAccount(
        Request $request,
        EntityManagerInterface $em,
        Security $security,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();


        if ($request->isMethod('POST')) {
            $submittedPassword = $request->request->get('password');

            if (!$submittedPassword || !$passwordHasher->isPasswordValid($user, $submittedPassword)) {
                $this->addFlash('danger', 'Mot de passe incorrect.');
                return $this->redirectToRoute('app_delete_account');
            }

            $em->remove($user);
            $em->flush();

            $security->logout(false);
            $request->getSession()->invalidate();

            $this->addFlash('success', 'Compte supprimé avec succès.');
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('user/delete_confirm.html.twig');
    }
}


