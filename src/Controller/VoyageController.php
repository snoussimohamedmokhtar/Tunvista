<?php

namespace App\Controller;

use App\Entity\Voyage;
use App\Form\VoyageType;
use App\Repository\VoyageRepository;
use App\Service\QrCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Options;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/voyage')]
class VoyageController extends AbstractController
{
    #[Route('/', name: 'app_voyage_index', methods: ['GET'])]
    public function index(Request $request, VoyageRepository $voyageRepository): Response
    {
        // Get the current page number from the request query parameters
        $currentPage = $request->query->getInt('page', 1);

        // Define the number of items per page
        $perPage = 5;

        // Count the total number of voyages
        $totalVoyages = $voyageRepository->count([]);

        // Calculate the total number of pages
        $totalPages = ceil($totalVoyages / $perPage);

        // Calculate the offset for pagination
        $offset = ($currentPage - 1) * $perPage;

        // Retrieve voyages for the current page
        $voyages = $voyageRepository->findBy([], null, $perPage, $offset);

        return $this->render('voyage/index.html.twig', [
            'voyages' => $voyages,
            'perPage' => $perPage, // Pass the number of items per page
            'currentPage' => $currentPage, // Pass the current page number
            'totalPages' => $totalPages, // Pass the total number of pages
        ]);
    }

    #[Route('/app_voyage_index_front', name: 'app_voyage_index_front', methods: ['GET'])]
    public function index_front(Request $request, VoyageRepository $voyageRepository): Response
    {
        // Get the current page number from the request query parameters
        $currentPage = $request->query->getInt('page', 1);

        // Define the number of items per page
        $perPage = 5;

        // Count the total number of voyages
        $totalVoyages = $voyageRepository->count([]);

        // Calculate the total number of pages
        $totalPages = ceil($totalVoyages / $perPage);

        // Calculate the offset for pagination
        $offset = ($currentPage - 1) * $perPage;

        // Retrieve voyages for the current page
        $voyages = $voyageRepository->findBy([], null, $perPage, $offset);

        return $this->render('voyage/index_front.html.twig', [
            'voyages' => $voyages,
            'perPage' => $perPage, // Pass the number of items per page
            'currentPage' => $currentPage, // Pass the current page number
            'totalPages' => $totalPages, // Pass the total number of pages
        ]);
    }

    #[Route('/new', name: 'app_voyage_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voyage = new Voyage();
        $form = $this->createForm(VoyageType::class, $voyage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if needed
                }

                $voyage->setImage($newFilename);
            }

            $entityManager->persist($voyage);
            $entityManager->flush();

            return $this->redirectToRoute('app_voyage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage/new.html.twig', [
            'voyage' => $voyage,
            'form' => $form,
        ]);
    }

    // Other methods in the controller...

#[Route('/{id}', name: 'app_voyage_show', methods: ['GET'])]
    public function show(Voyage $voyage, VoyageRepository $voyageRepository, QrCodeGenerator $qrCodeGenerator): Response
    {
        $qrCodes = [];
        $voyages = $voyageRepository->findAll();
        foreach ($voyages as $voyage) {
            $user = $voyage->getId();
            $qrCode = $qrCodeGenerator->createQrCode($voyage);
            $qrCodes[$voyage->getId()] = $qrCode->getString();
        }
        return $this->render('voyage/show.html.twig', [
            'voyage' => $voyage,
            'qrCodes' => $qrCodes,
        ]);
    }
    #[Route('/front/{id}', name: 'app_voyage_front_show', methods: ['GET'])]
    public function showfront(Voyage $voyage, VoyageRepository $voyageRepository, QrCodeGenerator $qrCodeGenerator): Response
    {



        // Generate QR code for each annonce's user and store it in $qrCodes array


        return $this->render('voyage/showfront.html.twig', [
            'voyage' => $voyage,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_voyage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voyage $voyage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoyageType::class, $voyage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_voyage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage/edit.html.twig', [
            'voyage' => $voyage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voyage_delete', methods: ['POST'])]
    public function delete(Request $request, Voyage $voyage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voyage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($voyage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_voyage_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/generate-pdf', name: 'voyage_generate_pdf')]
    public function generatePdf(Voyage $voyage): Response
    {
        // Get the HTML content of the page you want to convert to PDF
        $html = $this->renderView('voyage/show_pdf.html.twig', [
            // Pass any necessary data to your Twig template
            'voyage' => $voyage,
        ]);

        // Configure Dompdf options
        $options = new Options();
        $options->get('isHtml5ParserEnabled', true);

        // Instantiate Dompdf with the configured options
        $dompdf = new Dompdf($options);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Set response headers for PDF download
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="voyage.pdf"');

        return $response;
    }


//    #[Route('/statistique', name: 'app_voyage_statistique', methods: ['GET'])]
//     public function statistique(VoyageRepository $voyageRepository): Response
//     {
//         $statistics = $voyageRepository->findStatistics();

//         return $this->render('voyage/stat.html.twig', [
//             'statistics' => $statistics,
//         ]);
//     }
    // #[Route('/stats', name: 'app_stat', methods: ['GET'])]
    // public function statistics(VoyageRepository $voyageRepository): Response
    // {
    //     $data = $voyageRepository->createQueryBuilder('v')
    //         ->select('v.DateArrive, COUNT(v.id) as count')
    //         ->groupBy('v.DateArrive')
    //         ->getQuery()
    //         ->getResult();

    //     return $this->render('voyage/stat.html.twig', [
    //         'data' => $data,
    //     ]);
    // }



    #[Route('/search', name: 'app_voyage_search', methods: ['GET'])]
    public function search(Request $request, VoyageRepository $voyageRepository): JsonResponse
    {
        $query = $request->query->get('q');
        $results = $voyageRepository->findBySearchQuery($query); // Implement findBySearchQuery method in your repository

        $formattedResults = [];
        foreach ($results as $result) {
            // Format results as needed
            $formattedResults[] = [
                'Programme' => $result->getProgramme(),
                'Prix' => $result->getPrix(),

                // Add other fields as needed
            ];
        }

        return new JsonResponse($formattedResults);
    }


}
