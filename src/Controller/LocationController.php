<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\Location1Type;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

#[Route('/location')]
class LocationController extends AbstractController
{
    #[Route('/', name: 'app_location_index', methods: ['GET'])]
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render(view: 'location/index.html.twig', parameters: [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = new Location();
        $form = $this->createForm(Location1Type::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('app_location_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/new.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    #[Route('/{id_location}', name: 'app_location_show', methods: ['GET'])]
    public function show(Location $location): Response
    {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    #[Route('/{id_location}/edit', name: 'app_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Location1Type::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('location/edit.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    #[Route('/{id_location}', name: 'app_location_delete', methods: ['POST'])]
    public function delete(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$location->getIdLocation(), $request->request->get('_token'))) {
            $entityManager->remove($location);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/locations/tri', name: 'app_location_tri', methods: ['POST'])]
    public function sortedList(EntityManagerInterface $entityManager): Response
    {
        $locations = $entityManager->getRepository(Location::class)->findBy([], ['date_debut' => 'ASC']);

        return $this->render('location/index.html.twig', [
            'locations' => $locations,
        ]);
    }
    #[Route('/locations/pdf', name: 'app_location_pdf')]
    public function generatePdf(): Response
    {
        // Récupérer les données des locations
        $locations = $this->getDoctrine()->getRepository(Location::class)->findAll();

        // Créer un nouvel objet TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Définir le titre du document
        $pdf->SetTitle('Tunvista - Tableau Des Locations');

        // Ajouter une page
        $pdf->AddPage();

        // Ajouter une image
        $image_file = 'C:/xampp/htdocs/FirstProject/public/img/Logo_ESPRIT_Ariana.jpg';
        $pdf->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Ajouter un titre coloré
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(255, 0, 0); // Couleur rouge
        $pdf->Cell(0, 10, 'Tunvista', 0, true, 'C');

        // Ajouter un sous-titre
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 0, 0); // Couleur noire
        $pdf->Cell(0, 10, 'Tableau Des Locations', 0, true, 'C');

        // Créer le tableau des locations
        $html = '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr><th>ID</th><th>Date de début</th><th>Date de fin</th><th>Client</th><th>Voiture</th></tr>';
        foreach ($locations as $location) {
            $html .= '<tr>';
            $html .= '<td>' . $location->getIdLocation() . '</td>';
            $html .= '<td>' . $location->getDateDebut()->format('Y-m-d') . '</td>';
            $html .= '<td>' . $location->getDateFin()->format('Y-m-d') . '</td>';
            $html .= '<td>' . $location->getClient() . '</td>';
            $html .= '<td>' . $location->getVoiture() . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        // Écrire le contenu HTML dans le PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Envoyer le PDF en réponse
        return new Response($pdf->Output('tableau_locations.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="tableau_locations.pdf"',
        ]);
    }
    #[Route('/locations/recherche-par-id', name: 'recherche_par_id_location')]
    public function rechercheParIdLocation(Request $request): Response
    {
        // Récupérer l'ID de location à rechercher depuis la requête
        $idLocation = $request->query->get('id_location');

        // Vérifier si l'ID de location est défini
        if ($idLocation !== null) {
            // Récupérer les locations correspondant à l'ID de location
            $locations = $this->getDoctrine()->getRepository(Location::class)->rechercheParIdLocation($idLocation);
        } else {
            // Si aucun ID de location n'est spécifié, renvoyer toutes les locations
            $locations = $this->getDoctrine()->getRepository(Location::class)->findAll();
        }

        // Renvoyer les locations trouvées à la vue
        return $this->render('location/index.html.twig', [
            'locations' => $locations,
        ]);
    }


}
