<?php

namespace App\Controller;

use App\Repository\PageBlockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(PageBlockRepository $pageBlockRepository): Response
    {
        // On indexe les blocs par slug
        $blocks = [];
        foreach ($pageBlockRepository->findAll() as $block) {
            $blocks[$block->getSlug()] = $block;
        }

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'pageBlocks' => $blocks,
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('page/mentions_legales.html.twig');
    }
}

