<?php

namespace App\Controller\Admin;

use App\Entity\Espece;
use App\Form\EspeceType;
use App\Repository\EspeceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/especes', name: 'admin_espece_')]
#[IsGranted('ROLE_ADMIN')]
class EspeceAdminController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        EspeceRepository $repo,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $query = $repo->createQueryBuilder('e')
            ->orderBy('e.nomCommun', 'ASC')
            ->getQuery();

        $limit = $request->query->getInt('limit', 5);
        if (!in_array($limit, [5, 10, 20], true)) {
            $limit = 5;
        }

        $especes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('admin/espece/admin.html.twig', [
            'especes' => $especes,
            'nombreEspeces' => $repo->count([]),
            'limit' => $limit,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $espece = new Espece();
        $form = $this->createForm(EspeceType::class, $espece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($espece);
            $em->flush();

            $this->addFlash('success', 'Espèce créée.');
            return $this->redirectToRoute('admin_espece_list');
        }

        return $this->render('admin/espece/new.html.twig', [
            'form' => $form,
            'espece' => $espece,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Espece $espece, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EspeceType::class, $espece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Espèce mise à jour.');
            return $this->redirectToRoute('admin_espece_list');
        }

        return $this->render('admin/espece/edit.html.twig', [
            'form' => $form,
            'espece' => $espece,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Espece $espece, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$espece->getId(), $request->request->get('_token'))) {
            $em->remove($espece);
            $em->flush();
            $this->addFlash('danger', 'Espèce supprimée.');
        }

        return $this->redirectToRoute('admin_espece_list');
    }
}

