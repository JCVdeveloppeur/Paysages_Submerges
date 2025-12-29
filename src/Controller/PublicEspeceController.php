<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Form\EspeceSearchType;
use App\Repository\EspeceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/especes')]
class PublicEspeceController extends AbstractController
{
    #[Route('/', name: 'espece_public_index', methods: ['GET'])]
    public function index(Request $request, EspeceRepository $especeRepository, PaginatorInterface $paginator): Response
    {
        // Création du formulaire (GET pour inclure les données dans l’URL)
        $form = $this->createForm(EspeceSearchType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        // Récupération sécurisée du terme de recherche
        $search = $form->isSubmitted() && $form->isValid()
            ? $form->get('nom')->getData()
            : null;

        // Construction dynamique de la requête
        $queryBuilder = $especeRepository->createQueryBuilder('e')
            ->orderBy('e.nomCommun', 'ASC');

        if (!empty($search)) {
            $queryBuilder
                ->andWhere('LOWER(e.nomCommun) LIKE :search OR LOWER(e.nomScientifique) LIKE :search OR LOWER(e.biotope) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower($search) . '%');
        }

        // Limit (6/9/12) : sécurisé comme pour maladies
        $limitRaw = $request->query->get('limit', 6);
        $limit = ctype_digit((string) $limitRaw) ? (int) $limitRaw : 6;

        // (optionnel) on borne pour éviter 999 items/page
        if (!in_array($limit, [6, 9, 12], true)) {
            $limit = 6;
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('espece/public_index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView(),
            'limit' => $limit,
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



