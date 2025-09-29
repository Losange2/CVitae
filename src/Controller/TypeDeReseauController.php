<?php

namespace App\Controller;

use App\Entity\TypeDeReseau;
use App\Form\TypeDeReseauType;
use App\Repository\TypeDeReseauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/type/reseau')]
final class TypeDeReseauController extends AbstractController
{
    #[Route(name: 'app_type_de_reseau_index', methods: ['GET'])]
    public function index(TypeDeReseauRepository $typeDeReseauRepository): Response
    {
        return $this->render('type_de_reseau/index.html.twig', [
            'type_de_reseaus' => $typeDeReseauRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_de_reseau_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeDeReseau = new TypeDeReseau();
        $form = $this->createForm(TypeDeReseauType::class, $typeDeReseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeDeReseau);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_de_reseau_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_de_reseau/new.html.twig', [
            'type_de_reseau' => $typeDeReseau,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_de_reseau_show', methods: ['GET'])]
    public function show(TypeDeReseau $typeDeReseau): Response
    {
        return $this->render('type_de_reseau/show.html.twig', [
            'type_de_reseau' => $typeDeReseau,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_de_reseau_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeDeReseau $typeDeReseau, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeDeReseauType::class, $typeDeReseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_de_reseau_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_de_reseau/edit.html.twig', [
            'type_de_reseau' => $typeDeReseau,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_de_reseau_delete', methods: ['POST'])]
    public function delete(Request $request, TypeDeReseau $typeDeReseau, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeDeReseau->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typeDeReseau);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_de_reseau_index', [], Response::HTTP_SEE_OTHER);
    }
}
