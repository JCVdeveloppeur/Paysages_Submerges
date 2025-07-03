<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Repository\EspeceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/especes')]
class PublicEspeceController extends AbstractController
{
    #[Route('/', name: 'espece_public_index', methods: ['GET'])]
    public function index(EspeceRepository $especeRepository): Response
    {
        $especes = $especeRepository->findBy([], ['nomCommun' => 'ASC']);

        return $this->render('espece/public_index.html.twig', [
            'especes' => $especes,
        ]);
    }

    #[Route('/{id}', name: 'espece_public_show', methods: ['GET'])]
    public function show(Espece $espece): Response
    {
        return $this->render('espece/show.html.twig', [
            'espece' => $espece,
        ]);
    }
}

