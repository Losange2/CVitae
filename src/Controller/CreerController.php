<?php

namespace App\Controller;
use App\Repository\CvRepository;
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
            return $this->redirectToRoute('app_cv_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('creer/index.html.twig', [
            'controller_name' => 'CreerController',
            'cv' => $cv,
            'form' => $form,
        ]);
    }
}