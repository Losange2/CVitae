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
        $cv = $cvRepository->findAll();
        if (!$cv) {
            throw $this->createNotFoundException(
          'No CV found'
            );
        }

        $cv = new \App\Entity\Cv();
        $cv->setLeClient($this->getUser());
        $form = $this->createForm(\App\Form\CvType::class, $cv);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cv);
            $entityManager->flush();
            return $this->redirectToRoute('app_creer_remplissage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('creer/index.html.twig', [
            'controller_name' => 'CreerController',
            'cv' => $cv,
            'form' => $form,
        ]);
    }

    #[Route('/creer/remplissage', name: 'app_creer_remplissage')]
    public function remplissage(PointRepository $pointRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $point = $pointRepository->findAll();
        if (!$point) {
            throw $this->createNotFoundException(
          'No point found'
            );
        }
        $point = new \App\Entity\Point();
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
            'form' => $form,
        ]);

    }
}