<?php

namespace App\Controller;
use App\Repository\CvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class IndexController extends AbstractController

{
#[Route('/', name: 'app_index')]
public function index(CvRepository $cvRepository): Response
{
    $user = $this->getUser();
    $cvs = $cvRepository->findBy(['le_client' => $user]);

    $points = [];
    foreach ($cvs as $cv) {
        foreach ($cv->getLesPoints() as $point) {
            $points[] = $point;
        }
    }

    $categories = [];
    foreach ($points as $point) {
        $categories[] = $point->getLaCate();
    }


    return $this->render('index/index.html.twig', [
        'cvs' => $cvs,
        'points' => $points,
        'categories' => $categories,
    ]);

    
}

}