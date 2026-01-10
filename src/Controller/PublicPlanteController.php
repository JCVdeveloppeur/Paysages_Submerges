<?php

namespace App\Controller;

use App\Entity\Plante;
use App\Repository\PlanteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/plantes')]
final class PublicPlanteController extends AbstractController
{
    #[Route('/', name: 'plante_public_index', methods: ['GET'])]
    public function index(
        Request $request,
        PlanteRepository $planteRepository,
        PaginatorInterface $paginator
    ): Response {
        $nom = trim((string) $request->query->get('nom', ''));
        $biotope = $request->query->get('biotope');

        $limitRaw = $request->query->get('limit', 9);
        $limit = ctype_digit((string) $limitRaw) ? (int) $limitRaw : 9;
        if (!in_array($limit, [6, 9, 12], true)) {
            $limit = 9;
        }

        $qb = $planteRepository->createQueryBuilder('p')
            ->orderBy('p.nomCommun', 'ASC');

        $allowed = ['amerique-sud','amerique-centrale','asiatique','africain','australien','europeen','eaux-saumatres','mangroves','autre'];

        $biotopeLabels = [
            'amerique-sud'       => 'Amérique du sud',
            'amerique-centrale'  => 'Amérique centrale',
            'asiatique'          => 'Asie du Sud-Est',
            'africain'           => 'Afrique',
            'australien'         => 'Australie',
            'europeen'           => 'Europe',
            'eaux-saumatres'     => 'Eaux saumâtres',
            'mangroves'          => 'Mangroves',
            'autre'              => 'Autre',
        ];

        $currentBiotope = in_array($biotope, $allowed, true) ? $biotope : null;
        $currentBiotopeLabel = $currentBiotope ? ($biotopeLabels[$currentBiotope] ?? null) : null;

        if ($currentBiotope) {
            $qb->andWhere('p.biotope = :bio')->setParameter('bio', $currentBiotope);
        }

        if ($nom !== '') {
            $qb->andWhere('p.nomCommun LIKE :q OR p.nomScientifique LIKE :q')
            ->setParameter('q', '%' . $nom . '%');
        }

        // Recherche
        if ($nom !== '') {
            $qb->andWhere('p.nomCommun LIKE :q OR p.nomScientifique LIKE :q')
               ->setParameter('q', '%' . $nom . '%');
        }

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('plante/index.html.twig', [
            'pagination' => $pagination,
            'limit' => $limit,
            'nom' => $nom,
            'currentBiotope' => $currentBiotope,
            'currentBiotopeLabel' => $currentBiotopeLabel,
        ]);
    }

    #[Route('/{id}', name: 'plante_public_show', methods: ['GET'])]
    public function show(Plante $plante, Request $request, PlanteRepository $planteRepository): Response
    {
    $biotope = $request->query->get('biotope');
    $nom     = $request->query->get('nom', '');
    $limit   = $request->query->get('limit', 9);
    $page    = $request->query->get('page', 1);

    $backUrl = $this->generateUrl('plante_public_index', [
        'biotope' => $biotope,
        'nom' => $nom,
        'limit' => $limit,
        'page' => $page,
    ]);

    $prevUrl = null;
    $nextUrl = null;

    if ($biotope || $nom) {
        $ids = $planteRepository->findIdsForExplorer($biotope, $nom);
        $pos = array_search($plante->getId(), $ids, true);

        if ($pos !== false) {
            $prevId = $ids[$pos - 1] ?? null;
            $nextId = $ids[$pos + 1] ?? null;

            $qs = http_build_query([
                'biotope' => $biotope,
                'nom' => $nom,
                'limit' => $limit,
                'page' => $page,
            ]);

            if ($prevId) $prevUrl = $this->generateUrl('plante_public_show', ['id' => $prevId]) . '?' . $qs;
            if ($nextId) $nextUrl = $this->generateUrl('plante_public_show', ['id' => $nextId]) . '?' . $qs;
        }
    }

    return $this->render('plante/public_show.html.twig', [
        'plante' => $plante,
        'backUrl' => $backUrl,
        'prevUrl' => $prevUrl,
        'nextUrl' => $nextUrl,
        'currentBiotope' => $biotope,
    ]);
    }
}



