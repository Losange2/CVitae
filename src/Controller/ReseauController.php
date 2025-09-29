<?php

namespace App\Controller;

use App\Entity\Reseau;
use App\Form\ReseauType;
use App\Repository\ReseauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/reseau')]
final class ReseauController extends AbstractController
{
    #[Route(name: 'app_reseau_index', methods: ['GET'])]
    public function index(ReseauRepository $reseauRepository): Response
    {
        return $this->render('reseau/index.html.twig', [
            'reseaus' => $reseauRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reseau_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reseau = new Reseau();
        $form = $this->createForm(ReseauType::class, $reseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reseau);
            $entityManager->flush();

            return $this->redirectToRoute('app_reseau_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reseau/new.html.twig', [
            'reseau' => $reseau,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reseau_show', methods: ['GET'])]
    public function show(Reseau $reseau): Response
    {
        return $this->render('reseau/show.html.twig', [
            'reseau' => $reseau,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reseau_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reseau $reseau, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReseauType::class, $reseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reseau_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reseau/edit.html.twig', [
            'reseau' => $reseau,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reseau_delete', methods: ['POST'])]
    public function delete(Request $request, Reseau $reseau, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reseau->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reseau);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reseau_index', [], Response::HTTP_SEE_OTHER);
    }
}
