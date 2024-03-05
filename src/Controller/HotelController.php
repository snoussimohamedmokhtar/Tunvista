<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Form\HotelType;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\Mapping as ORM;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;



#[Route('/hotel')]
class HotelController extends AbstractController
{
    #[Route('/', name: 'app_hotel_index', methods: ['GET'])]
    public function index(HotelRepository $hotelRepository): Response
{
    $hotels = $hotelRepository->findAll();
    dump($hotels); 
    return $this->render('hotel/index.html.twig', [
        'hotels' => $hotels,
    ]);
}
#[Route('/search', name: 'app_hotel_search', methods: ['GET'])]
public function search(Request $request, HotelRepository $HotelRepository): JsonResponse
{
    $query = $request->query->get('q');
    $results = $HotelRepository->findBySearchQuery($query); // Implement findBySearchQuery method in your repository

    $formattedResults = [];
    foreach ($results as $result) {
        // Format results as needed
        $formattedResults[] = [
            'idH' => $result->getIdH(),
            'Nom_hotel' => $result->getNomHotel(),
            'Nbre_etoile' => $result->getNbreEtoile(),
            'Adresse_hotel' => $result->getAdresseHotel(),
            'prix_nuit' => $result->getPrixNuit(),
            'image' => $result->getImage(),
            // Add other fields as needed
        ];
    }

    return new JsonResponse($formattedResults);
}
#[Route('/trieasc', name: 'app_hotel_trieasc', methods: ['GET'])]
public function ascendingAction(HotelRepository $hotelRepository)
{
   return $this->render('hotel/index.html.twig', [
       'hotels' => $hotelRepository->findAllAscending("h.prix_nuit"),
   ]);
}

#[Route('/triedesc', name: 'app_hotel_triedesc', methods: ['GET'])]
public function descendingAction(HotelRepository $hotelRepository)
{
  
   return $this->render('hotel/index.html.twig', [
       'hotels' => $hotelRepository->findAllDescending("h.prix_nuit"),
   ]);

}
/**
     * @Route("/pdf", name="PDF_Hotel", methods={"GET"})
     */
    public function pdf(HotelRepository $hotelRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('hotel/pdf.html.twig', [
            'hotels' => $hotelRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        return new Response($dompdf->output(), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
        ]);
    }
#[Route('/new', name: 'app_hotel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
       
        $Hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $Hotel);
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
                }
    
                $Hotel->setImage($newFilename);
            }
    
            $entityManager->persist($Hotel); 
            $entityManager->flush(); 

            return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('hotel/new.html.twig', [
            'hotel' => $Hotel,
            'form' => $form,
        ]);
    }

    

    #[Route('/{idH}', name: 'app_hotel_show', methods: ['GET'])]
    public function show(Hotel $hotel): Response
    {
        return $this->render('hotel/show.html.twig', [
            'hotel' => $hotel,
        ]);
    }

    #[Route('/{idH}/edit', name: 'app_hotel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HotelType::class, $hotel);
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
                }
           
                $hotel->setImage($newFilename);
            }
            $entityManager->flush();
                return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);

            }
    
        return $this->renderForm('hotel/edit.html.twig', [
            'hotel' => $hotel,
            'form' => $form,
        ]);
    }


    #[Route('/{idH}', name: 'app_hotel_delete', methods: ['POST'])]
    public function delete(Request $request, Hotel $hotel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hotel->getIdH(), $request->request->get('_token'))) {
            $entityManager->remove($hotel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hotel_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/front', name: 'app_hotel_front')]
    public function front(): Response
    {
        $hotels = $this->getDoctrine()->getRepository(Hotel::class)->findAll();

        return $this->render('hotel/front.html.twig', [
            'hotels' => $hotels, 
        ]); 
        
    }
  
    
}

