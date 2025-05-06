<?php

namespace App\Controller;

use App\Repository\EspeceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EspeceController extends AbstractController
{
    #[Route('/espece/{id}', name: 'app_espece_show')]
    public function show(EspeceRepository $especeRepository, int $id): Response
    {
        $espece = $especeRepository->find($id);

        if (!$espece) {
            throw $this->createNotFoundException('Espèce non trouvée.');
        }

        return $this->render('espece/show.html.twig', [
            'espece' => $espece,
        ]);
    }

    #[Route('/especes', name: 'app_especes_list')]
    public function list(EspeceRepository $especeRepository): Response
    {
        $especes = $especeRepository->findAll();

        return $this->render('espece/list.html.twig', [
            'especes' => $especes,
        ]);
    }

    #[Route('/espece', name: 'app_espece_redirect')]
    public function redirectToList(): Response
    {
        return $this->redirectToRoute('app_especes_list');
    }
}
