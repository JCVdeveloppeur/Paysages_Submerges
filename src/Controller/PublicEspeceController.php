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
    // Formulaire de recherche (GET)
    $form = $this->createForm(EspeceSearchType::class, null, [
        'method' => 'GET',
    ]);
    $form->handleRequest($request);

    $search = ($form->isSubmitted() && $form->isValid())
        ? $form->get('nom')->getData()
        : null;

    // --- Filtre biotope espèces (slug -> valeur DB espece.biotope) ---
    $biotopeSlug = $request->query->get('biotope'); 

    $biotopeMapEspece = [
        'amerique-sud'      => 'Amérique du sud',
        'amerique-centrale' => 'Amérique centrale',
        'asiatique'         => 'Asie du Sud-Est', // valeur chez toi
        'africain'          => 'Afrique',
        'australien'        => 'Australie',
        'europeen'          => 'Europe',
        'eaux-saumatres'    => 'Eaux saumâtres',
        'mangroves'         => 'Mangroves',
        'autre'             => 'Autre',
    ];

    $currentBiotopeLabel = ($biotopeSlug && isset($biotopeMapEspece[$biotopeSlug]))
        ? $biotopeMapEspece[$biotopeSlug]
        : null;

    // QueryBuilder
    $queryBuilder = $especeRepository->createQueryBuilder('e')
        ->orderBy('e.nomCommun', 'ASC');

    // Filtre biotope (si valide)
    if ($currentBiotopeLabel) {
        $queryBuilder
            ->andWhere('e.biotope = :biotope')
            ->setParameter('biotope', $currentBiotopeLabel);
    }

    // Filtre recherche
    if (!empty($search)) {
        $queryBuilder
            ->andWhere('LOWER(e.nomCommun) LIKE :search OR LOWER(e.nomScientifique) LIKE :search OR LOWER(e.biotope) LIKE :search')
            ->setParameter('search', '%' . mb_strtolower($search) . '%');
    }

    // Limit (6/9/12)
    $limitRaw = $request->query->get('limit', 6);
    $limit = ctype_digit((string) $limitRaw) ? (int) $limitRaw : 6;
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
        'currentBiotope' => $biotopeSlug,               // ex: "asiatique"
        'currentBiotopeLabel' => $currentBiotopeLabel,  // ex: "Asie du Sud-Est"
    ]);
}

    #[Route('/{id}', name: 'espece_public_show', methods: ['GET'])]
    public function show(
        Espece $espece,
        Request $request,
        EspeceRepository $especeRepository
    ): Response {
        $biotope = $request->query->get('biotope'); // slug
        $nom     = (string) $request->query->get('nom', '');
        $limit   = (int) $request->query->get('limit', 6);
        $page    = (int) $request->query->get('page', 1);

        $backUrl = $this->generateUrl('espece_public_index', [
            'biotope' => $biotope,
            'nom'     => $nom,
            'limit'   => $limit,
            'page'    => $page,
        ]);

        $prevUrl = null;
        $nextUrl = null;

        if ($biotope || $nom !== '') {
            $ids = $especeRepository->findIdsForExplorer($biotope, $nom);
            $pos = array_search($espece->getId(), $ids, true);

            if ($pos !== false) {
                $prevId = $ids[$pos - 1] ?? null;
                $nextId = $ids[$pos + 1] ?? null;

                $qs = http_build_query([
                    'biotope' => $biotope,
                    'nom'     => $nom,
                    'limit'   => $limit,
                    'page'    => $page,
                ]);

                if ($prevId) {
                    $prevUrl = $this->generateUrl('espece_public_show', ['id' => $prevId]) . '?' . $qs;
                }
                if ($nextId) {
                    $nextUrl = $this->generateUrl('espece_public_show', ['id' => $nextId]) . '?' . $qs;
                }
            }
        }

        return $this->render('espece/public_show.html.twig', [
            'espece'  => $espece,
            'backUrl' => $backUrl,
            'prevUrl' => $prevUrl,
            'nextUrl' => $nextUrl,
        ]);
    }
}



