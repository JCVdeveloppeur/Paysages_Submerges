<?php

namespace App\Controller;

use App\Entity\Espece;
use App\Form\EspeceType;
use App\Repository\EspeceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/especes')]
class EspeceController extends AbstractController
{
   #[Route('/', name: 'espece_index', methods: ['GET'])]
public function index(
    Request $request,
    EspeceRepository $especeRepository,
    PaginatorInterface $paginator
): Response {
    // limit sécurisé (comme maladies)
    $limitRaw = $request->query->get('limit', 6);
    $limit = ctype_digit((string) $limitRaw) ? (int) $limitRaw : 6;

    // (optionnel) garde-fou : uniquement 6/9/12
    if (!in_array($limit, [6, 9, 12], true)) {
        $limit = 6;
    }

    $qb = $especeRepository->createQueryBuilder('e')
        ->orderBy('e.nomCommun', 'ASC');

    $pagination = $paginator->paginate(
        $qb, // QueryBuilder OK
        $request->query->getInt('page', 1),
        $limit
    );

    return $this->render('espece/index.html.twig', [
        'pagination' => $pagination,
        'limit' => $limit,
    ]);
}

    #[Route('/ajouter', name: 'espece_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $espece = new Espece();
        $form = $this->createForm(EspeceType::class, $espece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($espece);
            $em->flush();

            $this->addFlash('success', 'Nouvelle espèce ajoutée avec succès !');
            return $this->redirectToRoute('espece_index');
        }

        return $this->render('espece/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'espece_show', methods: ['GET'])]
    public function show(Espece $espece): Response
    {
        return $this->render('espece/show.html.twig', [
            'espece' => $espece,
        ]);
    }

    #[Route('/{id}/modifier', name: 'espece_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Espece $espece, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EspeceType::class, $espece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Espèce modifiée avec succès.');
            return $this->redirectToRoute('espece_index');
        }

        return $this->render('espece/edit.html.twig', [
            'form' => $form->createView(),
            'espece' => $espece,
        ]);
    }

    #[Route('/{id}', name: 'espece_delete', methods: ['POST'])]
    public function delete(Request $request, Espece $espece, EntityManagerInterface $em): Response
    {
        
        if ($this->isCsrfTokenValid('delete' . $espece->getId(), $request->request->get('_token'))) {
            $em->remove($espece);
            $em->flush();
            $this->addFlash('danger', 'Espèce supprimée.');
        }

        return $this->redirectToRoute('espece_index');
    }
}


