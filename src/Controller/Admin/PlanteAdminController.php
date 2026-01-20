<?php

namespace App\Controller\Admin;

use App\Entity\Plante;
use App\Form\PlanteForm;
use App\Repository\PlanteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/plantes', name: 'admin_plante_')]
#[IsGranted('ROLE_ADMIN')]
class PlanteAdminController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        PlanteRepository $planteRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $query = $planteRepository->createQueryBuilder('p')
            ->orderBy('p.nomCommun', 'ASC')
            ->getQuery();

        $limit = $request->query->getInt('limit', 5);
        if (!in_array($limit, [5, 10, 20], true)) {
            $limit = 5;
        }

        $plantes = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('admin/plante/admin.html.twig', [
            'plantes' => $plantes,
            'nombrePlantes' => $planteRepository->count([]),
            'limit' => $limit,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET','POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $plante = new Plante();
        $form = $this->createForm(PlanteForm::class, $plante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $safeFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move($this->getParameter('plantes_upload_dir'), $newFilename);
                $plante->setImage($newFilename);
            }

            $em->persist($plante);
            $em->flush();

            $this->addFlash('success', 'Plante créée.');
            return $this->redirectToRoute('admin_plante_list');
        }

        return $this->render('admin/plante/new.html.twig', [
            'form' => $form,
            'plante' => $plante,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(
        Request $request,
        Plante $plante,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(PlanteForm::class, $plante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                // Supprime l’ancienne image si elle existe
                if ($plante->getImage()) {
                    $oldPath = $this->getParameter('plantes_upload_dir') . '/' . $plante->getImage();
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $safeFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move($this->getParameter('plantes_upload_dir'), $newFilename);
                $plante->setImage($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Plante mise à jour.');
            return $this->redirectToRoute('admin_plante_list');
        }

        return $this->render('admin/plante/edit.html.twig', [
            'form' => $form,
            'plante' => $plante,
        ]);
    }
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Plante $plante, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plante->getId(), $request->request->get('_token'))) {

            if ($plante->getImage()) {
                $path = $this->getParameter('plantes_upload_dir').'/'.$plante->getImage();
                if (is_file($path)) {
                    unlink($path);
                }
            }

            $em->remove($plante);
            $em->flush();
            $this->addFlash('danger', 'Plante supprimée.');
        }

        return $this->redirectToRoute('admin_plante_list');
    }
}
