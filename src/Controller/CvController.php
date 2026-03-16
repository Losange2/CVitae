<?php

namespace App\Controller;

use App\Entity\Cv;
use App\Form\CvType;
use App\Repository\CvRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;

#[Route('/cv')]
final class CvController extends AbstractController
{
    
    #[Route(name: 'app_cv_index', methods: ['GET'])]
    public function index(CvRepository $cvRepository): Response
    {
        return $this->render('cv/index.html.twig', [
            'cvs' => $cvRepository->findAll(),
        ]);
    }



    #[Route('/new', name: 'app_cv_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cv = new Cv();
        $cv->setLeClient($this->getUser());
        $form = $this->createForm(CvType::class, $cv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/photos',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'photoFilename' property to store the PDF file name
                // instead of its contents
                $cv->setPhotoFilename($newFilename);
            }

            $entityManager->persist($cv);
            $entityManager->flush();

            return $this->redirectToRoute('app_cv_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cv/new.html.twig', [
            'cv' => $cv,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cv_show', methods: ['GET'])]
    public function show(Cv $cv): Response
    {
        return $this->render('cv/show.html.twig', [
            'cv' => $cv,
        ]);
    }

    #[Route('/{id}/pdf', name: 'app_cv_pdf', methods: ['GET'])]
    public function exportPdf(Cv $cv, Pdf $snappyPdf): Response
    {
        // Allow public access to PDF export
        // if ($cv->getLeClient() !== $this->getUser()) {
        //      throw $this->createAccessDeniedException();
        // }

        $html = $this->renderView('cv/pdf.html.twig', [
            'cv' => $cv,
            'project_dir' => $this->getParameter('kernel.project_dir'),
        ]);

        return new PdfResponse(
            $snappyPdf->getOutputFromHtml($html, [
                'margin-top'    => 0,
                'margin-right'  => 0,
                'margin-bottom' => 0,
                'margin-left'   => 0,
                'enable-local-file-access' => true,
                'disable-smart-shrinking' => true,
                'disable-javascript' => false,
                'print-media-type' => false,
                'dpi' => 96,
                'zoom' => 1,
                'javascript-delay' => 1000, 
            ]),
            'cv_' . $cv->getTitre() . '.pdf'
        );
    }

    #[Route('/{id}/photo', name: 'app_cv_photo', methods: ['POST'])]
    public function uploadPhoto(Request $request, Cv $cv, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Validating CSRF token
        if (!$this->isCsrfTokenValid('upload_photo_' . $cv->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_cv_show', ['id' => $cv->getId()]);
        }

        // Ensure the user is the owner before allowing photo update
        if ($cv->getLeClient() !== $this->getUser()) {
             throw $this->createAccessDeniedException();
        }

        $photoFile = $request->files->get('photo');
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

            try {
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/photos',
                    $newFilename
                );
                
                // Remove old photo if exists? For now just overwrite reference.
                $cv->setPhotoFilename($newFilename);
                $entityManager->flush();
            } catch (FileException $e) {
                // handle exception
                $this->addFlash('error', 'Erreur lors de l\'upload de la photo.');
            }
        }

        return $this->redirectToRoute('app_cv_show', ['id' => $cv->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_cv_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cv $cv, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($cv->getLeClient() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CvType::class, $cv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/photos',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'photoFilename' property to store the PDF file name
                // instead of its contents
                $cv->setPhotoFilename($newFilename);
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_cv_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cv/edit.html.twig', [
            'cv' => $cv,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cv_delete', methods: ['POST'])]
    public function delete(Request $request, Cv $cv, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cv->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cv);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cv_index', [], Response::HTTP_SEE_OTHER);
    }
}
