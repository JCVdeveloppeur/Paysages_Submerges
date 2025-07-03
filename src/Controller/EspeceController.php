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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/admin/especes')]
class EspeceController extends AbstractController
{
    #[Route('/', name: 'espece_index', methods: ['GET'])]
    public function index(EspeceRepository $especeRepository): Response
    {
        return $this->render('espece/index.html.twig', [
            'especes' => $especeRepository->findAll(),
        ]);
    }

    #[Route('/ajouter', name: 'espece_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
    $espece = new Espece();
    $form = $this->createForm(EspeceType::class, $espece);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/especes',
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
            }

            $espece->setImage($newFilename);
        }

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
    public function edit(Request $request, Espece $espece, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
    $form = $this->createForm(EspeceType::class, $espece);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/especes',
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
            }

            $espece->setImage($newFilename);
        }

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

