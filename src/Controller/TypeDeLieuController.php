<?php

namespace App\Controller;

use App\Entity\TypeDeLieu;
use App\Form\TypeDeLieuType;
use App\Repository\TypeDeLieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/type/de/lieu')]
final class TypeDeLieuController extends AbstractController
{
    #[Route(name: 'app_type_de_lieu_index', methods: ['GET'])]
    public function index(TypeDeLieuRepository $typeDeLieuRepository): Response
    {
        return $this->render('type_de_lieu/index.html.twig', [
            'type_de_lieus' => $typeDeLieuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_de_lieu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeDeLieu = new TypeDeLieu();
        $form = $this->createForm(TypeDeLieuType::class, $typeDeLieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeDeLieu);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_de_lieu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_de_lieu/new.html.twig', [
            'type_de_lieu' => $typeDeLieu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_de_lieu_show', methods: ['GET'])]
    public function show(TypeDeLieu $typeDeLieu): Response
    {
        return $this->render('type_de_lieu/show.html.twig', [
            'type_de_lieu' => $typeDeLieu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_de_lieu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeDeLieu $typeDeLieu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeDeLieuType::class, $typeDeLieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_de_lieu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_de_lieu/edit.html.twig', [
            'type_de_lieu' => $typeDeLieu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_de_lieu_delete', methods: ['POST'])]
    public function delete(Request $request, TypeDeLieu $typeDeLieu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeDeLieu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typeDeLieu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_de_lieu_index', [], Response::HTTP_SEE_OTHER);
    }
}
