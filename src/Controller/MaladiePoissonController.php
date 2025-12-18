<?php

namespace App\Controller;

use App\Entity\MaladiePoisson;
use App\Form\MaladiePoissonForm;
use App\Repository\MaladiePoissonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\MaladieSearchType;
use App\Service\RiskGlossary;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/maladie/poisson')]
final class MaladiePoissonController extends AbstractController
{
    #[Route(name: 'app_maladie_poisson_index', methods: ['GET'])]
public function index(
    MaladiePoissonRepository $repo,
    Request $request,
    PaginatorInterface $paginator
    ): Response {
    $form = $this->createForm(MaladieSearchType::class, null, [
        'method' => 'GET',
    ]);
    $form->handleRequest($request);

    $term  = $form->get('nom')->getData();
    $limitRaw = $request->query->get('limit', 6);
    $limit = ctype_digit((string) $limitRaw) ? (int) $limitRaw : 6;


    $qb = $repo->createQueryBuilder('m')
        ->orderBy('m.nom', 'ASC');

    if ($term) {
        $qb->andWhere('m.nom LIKE :term')
           ->setParameter('term', '%'.$term.'%');
    }

    $maladies = $paginator->paginate(
        $qb->getQuery(),
        $request->query->getInt('page', 1),
        $limit
    );

    return $this->render('maladie_poisson/index.html.twig', [
        'maladie_poissons' => $maladies,
        'form' => $form->createView(),
        'limit' => $limit,
    ]);
    }

    #[Route('/new', name: 'app_maladie_poisson_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maladiePoisson = new MaladiePoisson();
        $form = $this->createForm(MaladiePoissonForm::class, $maladiePoisson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/maladies';

                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0775, true);
                }

                $newFilename = uniqid('maladie_') . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $maladiePoisson->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'enregistrer l\'image.');
                }
            }

            $entityManager->persist($maladiePoisson);
            $entityManager->flush();
            $this->addFlash('success', 'Nouvelle pathologie ajoutée avec succès !');
            return $this->redirectToRoute('admin_maladies');
        }

        return $this->render('maladie_poisson/new.html.twig', [
            'maladie_poisson' => $maladiePoisson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_maladie_poisson_show', methods: ['GET'])]
    public function show(MaladiePoisson $maladiePoisson, RiskGlossary $riskGlossary): Response
    {
        $graviteValue = mb_strtolower((string) $maladiePoisson->getGravite(), 'UTF-8');

        $graviteLevel = match (true) {
            $graviteValue === 'faible' => 'faible',
            str_starts_with($graviteValue, 'moyen') => 'moyenne',
            str_starts_with($graviteValue, 'élev') || str_starts_with($graviteValue, 'eleve') => 'elevee',
            default => '',
        };

        $risk = $riskGlossary->get($graviteLevel);

        return $this->render('maladie_poisson/show.html.twig', [
            'maladie_poisson' => $maladiePoisson,
            'graviteLevel' => $graviteLevel,
            'risk' => $risk,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_maladie_poisson_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, MaladiePoisson $maladiePoisson, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaladiePoissonForm::class, $maladiePoisson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/maladies';

                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0775, true);
                }

                // supprimer l'ancienne image si elle existe
                if ($maladiePoisson->getImage()) {
                    $oldPath = $uploadsDir . '/' . $maladiePoisson->getImage();
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $newFilename = uniqid('maladie_') . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $maladiePoisson->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'enregistrer la nouvelle image.');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Pathologie modifiée avec succès !');
            return $this->redirectToRoute('admin_maladies');
        }

        return $this->render('maladie_poisson/edit.html.twig', [
            'maladie_poisson' => $maladiePoisson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_maladie_poisson_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, MaladiePoisson $maladiePoisson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maladiePoisson->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($maladiePoisson);
            $entityManager->flush();
            $this->addFlash('danger', 'Pathologie supprimée.');
            return $this->redirectToRoute('admin_maladies');

        }

        return $this->redirectToRoute('admin_maladies');
    }
}

