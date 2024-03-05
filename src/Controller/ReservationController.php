<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\HotelRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\KnpSnappyBundle;


#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
   

    #[Route('/trieasc', name: 'app_trieasc', methods: ['GET'])]
    public function ascendingAction(ReservationRepository $ReservationRepository)
    {
       return $this->render('Reservation/index.html.twig', [
           'reservations' => $ReservationRepository->findAllAscending("r.Date_arrivee"),
       ]);
    }
    
    #[Route('/triedesc', name: 'app_triedesc', methods: ['GET'])]
    public function descendingAction(ReservationRepository $ReservationRepository)
    {
      
       return $this->render('Reservation/index.html.twig', [
           'reservations' => $ReservationRepository->findAllDescending("r.Date_arrivee"),
       ]);
    
    }
    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,Pdf $knpSnappyPdf): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
            $invoiceHtml = $this->renderView('reservation/pdf_template.html.twig', [
                'reservation' => $reservation,
            ]);

            // Generate PDF from HTML
            $pdf = $knpSnappyPdf->getOutputFromHtml($invoiceHtml);

            // Return the PDF as a response for download
            return new Response(
                $pdf,
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="invoice.pdf"',
                ]
            );
           

            return $this->redirectToRoute('app_reservation_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
    #[Route('/{idR}', name: 'app_reservation_show', methods: ['GET'])]
    #[ParamConverter('reservation', class: 'App\Entity\Reservation')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{idR}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{idR}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getIdR(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
   
}
