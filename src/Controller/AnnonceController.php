<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;


#[Route('/annonce')]
class AnnonceController extends AbstractController
{
    #[Route('/', name: 'app_annonce_index', methods: ['GET', 'POST'])]
    public function index(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $searchTerm = $request->request->get('search', '');
        $type = $request->request->get('type', '');

        // Si le formulaire est soumis, vous pouvez également récupérer le type et le terme de recherche à partir de la requête POST
        if ($request->isMethod('POST')) {
            $searchTerm = $request->request->get('search');
            $type = $request->request->get('type');
        }

        // Rechercher les annonces en fonction du terme de recherche et du type
        $annonces = $annonceRepository->findBySearchTermAndType($searchTerm, $type);

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }
    #[Route('/new', name: 'app_annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id_annonce}', name: 'app_annonce_show', methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/{id_annonce}/edit', name: 'app_annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id_annonce}', name: 'app_annonce_delete', methods: ['POST'])]
    public function delete(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getIdAnnonce(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/annonces/tri', name: 'app_annonce_tri', methods: ['POST'])]
    public function sortedList(EntityManagerInterface $entityManager): Response
    {
        $annonces = $entityManager->getRepository(Annonce::class)->findBy([], ['date_debut' => 'ASC']);

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/annonces/pdf', name: 'app_annonce_pdf', methods: ['GET'])]
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
                <th align="center">Type</th>
            </tr>';

        // Populate table with data
        foreach ($annonces as $annonce) {
            $html .= '<tr>
                <td align="center">' . $annonce->getIdAnnonce() . '</td>
                <td align="center">' . $annonce->getDateDebut()->format('Y-m-d') . '</td>
                <td align="center">' . $annonce->getTypeA() . '</td>
            </tr>';
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
