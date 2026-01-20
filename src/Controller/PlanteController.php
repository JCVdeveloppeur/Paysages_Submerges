<?php

namespace App\Controller;

use App\Entity\Plante;
use App\Form\PlanteForm;
use App\Repository\PlanteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/plante')]
final class PlanteController extends AbstractController
{
    #[Route(name: 'app_plante_index', methods: ['GET'])]
    public function index(
        Request $request,
        PlanteRepository $planteRepository,
        PaginatorInterface $paginator
    ): Response {
        // Params GET (comme espèces/maladies)
        $nom = trim((string) $request->query->get('nom', ''));
        $limit = abs((int) $request->query->get('limit', 6));
        $page  = (int) $request->query->get('page', 1);

        // Sécurise limit
        if (!in_array($limit, [6, 9, 12], true)) {
            $limit = 6;
        }
        if ($page < 1) {
            $page = 1;
        }

        // QueryBuilder (recherche)
        $qb = $planteRepository->createQueryBuilder('p')
            ->orderBy('p.nomCommun', 'ASC');

        if ($nom !== '') {
           $qb->andWhere('p.nomCommun LIKE :q OR p.nomScientifique LIKE :q')
            ->setParameter('q', '%' . $nom . '%');
        }

        // Pagination
        $pagination = $paginator->paginate($qb->getQuery(), $page, $limit);

        return $this->render('plante/index.html.twig', [
            'pagination' => $pagination,
            'limit' => $limit,
            'nom' => $nom,
        ]);
    }

    #[Route('/new', name: 'app_plante_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
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

            $entityManager->persist($plante);
            $entityManager->flush();

            return $this->redirectToRoute('app_plante_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plante/new.html.twig', [
            'plante' => $plante,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_plante_show', methods: ['GET'])]
    public function show(Plante $plante): Response
    {
        return $this->render('plante/show.html.twig', [
            'plante' => $plante,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_plante_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plante $plante, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PlanteForm::class, $plante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                if ($plante->getImage()) {
                    $oldPath = $this->getParameter('plantes_upload_dir').'/'.$plante->getImage();
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $safeFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move($this->getParameter('plantes_upload_dir'), $newFilename);
                $plante->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_plante_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plante/edit.html.twig', [
            'plante' => $plante,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_plante_delete', methods: ['POST'])]
    public function delete(Request $request, Plante $plante, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plante->getId(), $request->request->get('_token'))) {
            if ($plante->getImage()) {
            $path = $this->getParameter('plantes_upload_dir').'/'.$plante->getImage();
            if (file_exists($path)) {
                @unlink($path);
            }
        }

            $entityManager->remove($plante);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plante_index', [], Response::HTTP_SEE_OTHER);
    }
}

