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

final class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function index(Request $request, CvRepository $cvRepository): Response
    {
        $query = $request->query->get('q', '');
    
        if ($query) {
            $cvs = $cvRepository->searchCvs($query);
        } else {
            $cvs = $cvRepository->findAll();
        }
    
        return $this->render('recherche/index.html.twig', [
            'cvs' => $cvs,
            'query' => $query,
    ]);
    }
}
