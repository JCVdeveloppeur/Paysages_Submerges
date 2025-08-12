<?php

namespace App\Controller\Admin;

use App\Entity\PageBlock;
use App\Form\PageBlockType;
use App\Repository\PageBlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/page-blocks', name: 'admin_page_block_')]
class PageBlockAdminController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(PageBlockRepository $repo): Response
    {
        return $this->render('admin/page_block/list.html.twig', [
            'blocks' => $repo->findBy([], ['updatedAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $block = new PageBlock();
        $form = $this->createForm(PageBlockType::class, $block);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($block);
            $em->flush();

            $this->addFlash('success', 'Bloc créé.');
            return $this->redirectToRoute('admin_page_block_list');
        }

        return $this->render('admin/page_block/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET','POST'])]
    public function edit(PageBlock $block, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PageBlockType::class, $block);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Bloc mis à jour.');
            return $this->redirectToRoute('admin_page_block_list');
        }

        return $this->render('admin/page_block/edit.html.twig', [
            'form' => $form,
            'block' => $block,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(PageBlock $block, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_page_block_'.$block->getId(), $request->request->get('_token'))) {
            $em->remove($block);
            $em->flush();
            $this->addFlash('success', 'Bloc supprimé.');
        }

        return $this->redirectToRoute('admin_page_block_list');
    }
}

