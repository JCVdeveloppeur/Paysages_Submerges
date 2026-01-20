<?php

namespace App\Controller\Admin;

use App\Entity\MaladiePoisson;
use App\Form\MaladiePoissonForm;
use App\Repository\MaladiePoissonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/maladies', name: 'admin_maladie_')]
#[IsGranted('ROLE_ADMIN')]
class MaladieAdminController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        MaladiePoissonRepository $repo,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $query = $repo->createQueryBuilder('m')
            ->orderBy('m.nom', 'ASC')
            ->getQuery();

        $limit = $request->query->getInt('limit', 5);
        if (!in_array($limit, [5, 10, 20], true)) {
            $limit = 5;
        }

        $maladies = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $limit
        );

        return $this->render('admin/maladie/admin.html.twig', [
            'maladies' => $maladies,
            'nombreMaladies' => $repo->count([]),
            'limit' => $limit,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $maladie = new MaladiePoisson();
        $form = $this->createForm(MaladiePoissonForm::class, $maladie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form->get('imageFile')->getData(), $maladie);

            $em->persist($maladie);
            $em->flush();

            $this->addFlash('success', 'Pathologie créée.');
            return $this->redirectToRoute('admin_maladie_list');
        }

        return $this->render('admin/maladie/new.html.twig', [
            'form' => $form,
            'maladie' => $maladie,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(Request $request, MaladiePoisson $maladie, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MaladiePoissonForm::class, $maladie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleImageUpload($form->get('imageFile')->getData(), $maladie, true);

            $em->flush();

            $this->addFlash('success', 'Pathologie mise à jour.');
            return $this->redirectToRoute('admin_maladie_list');
        }

        return $this->render('admin/maladie/edit.html.twig', [
            'form' => $form,
            'maladie' => $maladie,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, MaladiePoisson $maladie, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $maladie->getId(), $request->request->get('_token'))) {

            // Supprime aussi l’image si elle existe
            if ($maladie->getImage()) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/maladies';
                $oldPath = $uploadsDir . '/' . $maladie->getImage();
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $em->remove($maladie);
            $em->flush();
            $this->addFlash('danger', 'Pathologie supprimée.');
        }

        return $this->redirectToRoute('admin_maladie_list');
    }

    private function handleImageUpload(?UploadedFile $imageFile, MaladiePoisson $maladie, bool $replace = false): void
    {
        if (!$imageFile) {
            return;
        }

        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/maladies';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0775, true);
        }

        if ($replace && $maladie->getImage()) {
            $oldPath = $uploadsDir . '/' . $maladie->getImage();
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $newFilename = uniqid('maladie_') . '.' . $imageFile->guessExtension();

        try {
            $imageFile->move($uploadsDir, $newFilename);
            $maladie->setImage($newFilename);
        } catch (FileException) {
            $this->addFlash('danger', 'Impossible d\'enregistrer l\'image.');
        }
    }
}
