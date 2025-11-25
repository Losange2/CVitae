<?php

namespace App\Controller;

use App\Entity\Point;
use App\Entity\Cv;
use App\Form\PointType;
use App\Repository\PointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/point')]
final class PointController extends AbstractController
{
    #[Route(name: 'app_point_index', methods: ['GET'])]
    public function index(PointRepository $pointRepository): Response
    {
        return $this->render('point/index.html.twig', [
            'points' => $pointRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_point_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $point = new Point();

        // Si la requête vient du bouton POST de la page CV (sans soumettre le formulaire Symfony),
        // on enregistre l'id dans la session puis on redirige vers la même route en GET
        // afin d'afficher le formulaire pré-rempli sans exposer l'id dans l'URL.
        $cvId = $request->get('cv_id');
        $isFormSubmitted = $request->request->has($this->createForm(PointType::class, $point)->getName());
        if ($request->isMethod('POST') && $cvId && !$isFormSubmitted) {
            $request->getSession()->set('prefill_cv_id', $cvId);
            return $this->redirectToRoute('app_point_new');
        }

        // Récupérer l'id stocké en session (si présent) ou depuis la requête
        $cvId = $request->get('cv_id') ?? $request->getSession()->get('prefill_cv_id');
        if ($cvId) {
            $cv = $entityManager->getRepository(Cv::class)->find($cvId);
            // Vérifier que le CV existe et appartient bien à l'utilisateur connecté
            if ($cv && $cv->getLeClient() === $this->getUser()) {
                $point->setLeCv($cv);
            }
            // Retirer la valeur de session après usage
            $request->getSession()->remove('prefill_cv_id');
        }
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($point);
            $entityManager->flush();

            return $this->redirectToRoute('app_point_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('point/new.html.twig', [
            'point' => $point,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_point_show', methods: ['GET'])]
    public function show(Point $point): Response
    {
        return $this->render('point/show.html.twig', [
            'point' => $point,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_point_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Point $point, EntityManagerInterface $entityManager): Response
    {
        // Créer un formulaire simple avec uniquement le champ libelle
        $form = $this->createFormBuilder($point)
            ->add('libelle')
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Rediriger vers la page du CV pour voir immédiatement les modifications
            return $this->redirectToRoute('app_cv_show', ['id' => $point->getLeCv()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('point/edit.html.twig', [
            'point' => $point,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_point_delete', methods: ['POST'])]
    public function delete(Request $request, Point $point, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$point->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($point);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_point_index', [], Response::HTTP_SEE_OTHER);
    }
}
