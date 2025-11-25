<?php

namespace App\Controller;
use App\Repository\CvRepository;
use App\Repository\PointRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CreerController extends AbstractController
{
    #[Route('/creer', name: 'app_creer')]
    public function index(Request $request, CvRepository $cvRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Création d'un nouveau CV et gestion du formulaire
        $cv = new \App\Entity\Cv();
        $cv->setLeClient($this->getUser());

        $form = $this->createForm(\App\Form\CvType::class, $cv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cv);
            $entityManager->flush();

            // Stocker l'id du CV créé en session pour le remplissage
            $request->getSession()->set('cv_id_for_remplissage', $cv->getId());

            return $this->redirectToRoute('app_creer_remplissage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('creer/index.html.twig', [
            'controller_name' => 'CreerController',
            'cv' => $cv,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/creer/remplissage', name: 'app_creer_remplissage')]
    public function remplissage(PointRepository $pointRepository, EntityManagerInterface $entityManager, Request $request, CvRepository $cvRepository): Response
    {
        // Création d'un nouveau Point et gestion du formulaire de remplissage
        $point = new \App\Entity\Point();

        // Si la requête vient du bouton POST de la page CV (sans soumettre le formulaire Symfony),
        // on enregistre l'id dans la session puis on redirige vers la même route en GET
        $cvId = $request->get('cv_id');
        $isFormSubmitted = $request->request->has($this->createForm(\App\Form\PointType::class, $point)->getName());
        if ($request->isMethod('POST') && $cvId && !$isFormSubmitted) {
            $request->getSession()->set('cv_id_for_remplissage', $cvId);
            return $this->redirectToRoute('app_creer_remplissage');
        }

        // Récupérer l'id du CV depuis la session et pré-affecter le Point
        $cvId = $request->get('cv_id') ?? $request->getSession()->get('cv_id_for_remplissage');
        if ($cvId) {
            $cv = $cvRepository->find($cvId);
            // Vérifier que le CV existe et appartient bien à l'utilisateur connecté
            if ($cv && $cv->getLeClient() === $this->getUser()) {
                $point->setLeCv($cv);
            } else {
                // CV invalide ou n'appartient pas à l'utilisateur : nettoyer la session
                $request->getSession()->remove('cv_id_for_remplissage');
            }
        }

        $form = $this->createForm(\App\Form\PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($point);
            $entityManager->flush();

            return $this->redirectToRoute('app_creer_remplissage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('creer/remplissage.html.twig', [
            'controller_name' => 'CreerController',
            'point' => $point,
            'form' => $form->createView(),
        ]);

    }
}