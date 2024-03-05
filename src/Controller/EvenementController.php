<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    /*#[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }*/

    #[Route('/', name: 'app_evenement_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EvenementRepository $evenementRepository): Response
    {
        $evenementRepository->deleteExpiredEvents();
        // Retrieve search terms and sorting options from the request
        $searchTerm = $request->request->get('search', '');
        $titre = $request->request->get('titre', '');
        $sortBy = $request->query->get('sortBy', 'titre_e'); // Default sorting by titreA
        $sortDirection = $request->query->get('sortDirection', 'asc'); // Default sort direction is ascending

        /*$villeNotifa = $request->request->get('ville_a', ''); // Assuming you have a method to get the ville_notif value
        $usersWithMatchingVille = $userRepository->findBy(['ville' => $villeNotifa]);*/

        // Create notifications for each user with a matching ville_notif
        /*foreach ($usersWithMatchingVille as $user) {
            $notifa = new Notifa();
            $notifa->setUser($user);
            $notifa->setAnnonce($Annonce);
            // Set other notification attributes as needed
            $entityManager->persist($notifa);
        }*/


        $showModal = true;
        // Handle form submission
        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search');
            $sortBy = $request->request->get('sortBy', 'titre_e'); // Default sorting by titreA
            $sortDirection = $request->request->get('sortDirection', 'asc'); // Default sort direction is ascending
        }

        // Validate sorting options
        $validSortOptions = ['titre_e', 'description_e', 'ville_e', 'date_deb'];
        if (!in_array($sortBy, $validSortOptions)) {
            throw $this->createNotFoundException('Invalid sort option.');
        }

// Validate sort direction
        $validSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $validSortDirections)) {
            throw $this->createNotFoundException('Invalid sort direction.');
        }

// Retrieve evenements based on search term
        $evenements = $evenementRepository->findBySearchTermAndTitre($searchTerm, $titre);

// Sort evenements based on sorting options
        usort($evenements, function($a, $b) use ($sortBy, $sortDirection) {
            $getterA = 'get' . ucfirst($sortBy);
            $getterB = 'get' . ucfirst($sortBy);

            // Special handling for description_a and date_debut fields
            if ($sortBy === 'description_e') {
                $valueA = $a->getDescriptionE();
                $valueB = $b->getDescriptionE();
            } elseif ($sortBy === 'titre_e') {
                $valueA = $a->getTitreE();
                $valueB = $b->getTitreE();
            } elseif ($sortBy === 'ville_e') {
                $valueA = $a->getVilleE();
                $valueB = $b->getVilleE();
            }elseif ($sortBy === 'date_deb') {
                $valueA = $a->getDateDeb();
                $valueB = $b->getDateDeb();
            }else {
                $valueA = $a->$getterA();
                $valueB = $b->$getterB();
            }

            if ($sortDirection === 'asc') {
                return $valueA <=> $valueB;
            } else {
                return $valueB <=> $valueA;
            }
        });


        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'showModal' => $showModal,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }


    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_evenement}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        $mapsEven = $evenement->getMapsEven();
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
            'mapsEven' => $mapsEven,
        ]);
    }

    #[Route('/{id_evenement}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_evenement}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIdEvenement(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/evenements/tri', name: 'app_evenement_tri', methods: ['POST'])]
    public function sortedList(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager->getRepository(Evenement::class)->findBy([], ['date_deb' => 'ASC']);

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenements/pdf', name: 'app_evenement_pdf', methods: ['GET'])]
    public function generatePdf(): Response
    {
        // Fetch data from your database using Doctrine ORM
        $evenements = $this->getDoctrine()->getRepository(Evenement::class)->findAll();

        // Create new PDF instance
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document meta information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Author');
        $pdf->SetTitle('Evenement Table to PDF');
        $pdf->SetSubject('Evenement Table');
        $pdf->SetKeywords('TCPDF, PDF, Symfony, Doctrine');

        // Set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Add a page
        $pdf->AddPage();

        $image_file = 'C:/xampp/htdocs/FirstProject/public/img/Logo_ESPRIT_Ariana.jpg';
        $pdf->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(0, 0, 0); // Couleur rouge
        $pdf->Cell(0, 10, 'Tunvista', 0, 1, 'C');

        // Add title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Liste des Evenements', 0, 1, 'C');
        $pdf->Ln(10); // Add line break

        // Define table structure
        $html = '<table border="1">
            <tr>
                <th align="center">ID</th>
                <th align="center">Titre</th>
                <th align="center">Description</th>
                <th align="center">Ville</th>
                <th align="center">Date de début</th>

            </tr>';

        // Populate table with data
        foreach ($evenements as $evenement) {
            $html .= '<tr>
                <td align="center">' . $evenement->getIdEvenement() . '</td>
                <td align="center">' . $evenement->getTitreE() . '</td>
                <td align="center">' . $evenement->getDescriptionE() . '</td>
                <td align="center">' . $evenement->getVilleE() . '</td>
                <td align="center">' . $evenement->getDateDeb()->format('Y-m-d') . '</td>
            </tr>';
        }

        $html .= '</table>';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF document
        $pdf->Output('evenements.pdf', 'I');

        // Return a Symfony Response object
        return new Response();
    }
}
