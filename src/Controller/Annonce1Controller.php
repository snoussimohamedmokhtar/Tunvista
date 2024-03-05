<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;


#[Route('/annonce1')]
class Annonce1Controller extends AbstractController
{
    #[Route('/', name: 'app_annonce1_index', methods: ['GET', 'POST'])]
    public function index(Request $request, AnnonceRepository $annonceRepository, UserRepository $userRepository): Response
    {
        $annonceRepository->deleteExpiredAnnouncements();
        // Retrieve search terms and sorting options from the request
        $searchTerm = $request->request->get('search', '');
        $titre = $request->request->get('titre', '');
        $sortBy = $request->query->get('sortBy', 'titre_a'); // Default sorting by titreA
        $sortDirection = $request->query->get('sortDirection', 'asc'); // Default sort direction is ascending

        //$villeNotifa = $request->request->get('ville_a', ''); // Assuming you have a method to get the ville_notif value
        //$usersWithMatchingVille = $userRepository->findBy(['ville' => $villeNotifa]);

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
            $sortBy = $request->request->get('sortBy', 'titre_a'); // Default sorting by titreA
            $sortDirection = $request->request->get('sortDirection', 'asc'); // Default sort direction is ascending
        }

        // Validate sorting options
        $validSortOptions = ['titre_a', 'description_a', 'ville_a', 'date_debut'];
        if (!in_array($sortBy, $validSortOptions)) {
            throw $this->createNotFoundException('Invalid sort option.');
        }

// Validate sort direction
        $validSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $validSortDirections)) {
            throw $this->createNotFoundException('Invalid sort direction.');
        }

// Retrieve annonces based on search term
        $annonces = $annonceRepository->findBySearchTermAndTitre($searchTerm, $titre);

// Sort annonces based on sorting options
        usort($annonces, function($a, $b) use ($sortBy, $sortDirection) {
            $getterA = 'get' . ucfirst($sortBy);
            $getterB = 'get' . ucfirst($sortBy);

            // Special handling for description_a and date_debut fields
            if ($sortBy === 'description_a') {
                $valueA = $a->getDescriptionA();
                $valueB = $b->getDescriptionA();
            } elseif ($sortBy === 'titre_a') {
                $valueA = $a->getTitreA();
                $valueB = $b->getTitreA();
            } elseif ($sortBy === 'ville_a') {
                $valueA = $a->getVilleA();
                $valueB = $b->getVilleA();
            }elseif ($sortBy === 'date_debut') {
                $valueA = $a->getDateDebut();
                $valueB = $b->getDateDebut();
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


        return $this->render('annonce1/index.html.twig', [
            'annonces' => $annonces,
            'showModal' => $showModal,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }
    #[Route('/new', name: 'app_annonce1_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce1_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonce1/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id_annonce}', name: 'app_annonce1_show', methods: ['GET'])]
    public function show(Annonce $annonces): Response
    {
        return $this->render('annonce1/show.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/{id_annonce}/edit', name: 'app_annonce1_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonces, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonces);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce1_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonce1/edit.html.twig', [
            'annonces' => $annonces,
            'form' => $form,
        ]);
    }

    #[Route('/{id_annonce}', name: 'app_annonce1_delete', methods: ['POST'])]
    public function delete(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getIdAnnonce(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce1_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/annonces/tri', name: 'app_annonce1_tri', methods: ['POST'])]
    public function sortedList(EntityManagerInterface $entityManager): Response
    {
        $annonces = $entityManager->getRepository(Annonce::class)->findBy([], ['date_debut' => 'ASC']);

        return $this->render('annonce1/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/annonces/pdf', name: 'app_annonce1_pdf', methods: ['GET'])]
    public function generatePdf(): Response
    {
        // Fetch data from your database using Doctrine ORM
        $annonces = $this->getDoctrine()->getRepository(Annonce::class)->findAll();

        // Create new PDF instance
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document meta information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Author');
        $pdf->SetTitle('Annonce Table to PDF');
        $pdf->SetSubject('Annonce Table');
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
        $pdf->Cell(0, 10, 'Liste des Annonces', 0, 1, 'C');
        $pdf->Ln(10); // Add line break

        // Define table structure
        $html = '<table border="1">
        <tr>
            <th align="center">ID</th>
            <th align="center">Date de début</th>
            <th align="center">Description</th>
            <th align="center">Titre</th>
            <th align="center">Ville</th>
        </tr>';

        // Populate table with data if $annonces is not null
        if (!empty($annonces)) {
            foreach ($annonces as $annonce) {
                $html .= '<tr>
                <td align="center">' . $annonce->getIdAnnonce() . '</td>
                <td align="center">' . $annonce->getDateDebut()->format('Y-m-d') . '</td>
                <td align="center">' . $annonce->getDescriptionA() . '</td>
                <td align="center">' . $annonce->getTitreA() . '</td>
                <td align="center">' . $annonce->getVilleA() . '</td>
            </tr>';
            }
        }

        $html .= '</table>';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF document
        $pdf->Output('annonces.pdf', 'I');

        // Return a Symfony Response object
        return new Response();
    }

}
