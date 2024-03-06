<?php

namespace App\Controller;

use App\Entity\Maison;
use App\Form\MaisonType;
use App\Repository\MaisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use TCPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
#[Route('/maison')]
class MaisonController extends AbstractController
{
    #[Route('/', name: 'app_maison_index', methods: ['GET'])]
    public function index(Request $request,MaisonRepository $maisonRepository ,PaginatorInterface $paginator): Response
    {
       
          
    $maisons = $maisonRepository->findAll();

    // Calculate pharmacies per address
    $maisonsPerAdresse = [];
    foreach ($maisons as $maison) {
        $adresse = $maison->getAdresse();
        if (!isset($maisonsPerAdresse[$adresse])) {
            $maisonsPerAdresse[$adresse] = 1;
        } else {
            $maisonsPerAdresse[$adresse]++;
        }
    }

    // Render the template with pharmacies and pharmaciesPerAddress
    return $this->render('maison/index.html.twig', [
        'maisons' => $maisons,
        'maisonsPerAdresse' => $maisonsPerAdresse,
    ]);
    
    }


    #[Route('/new', name: 'app_maison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
       
        $maison = new Maison();
        $form = $this->createForm(MaisonType::class, $maison);
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
    
                
                $maison->setImage($newFilename);
            }
    
            $entityManager->persist($maison); 
            $entityManager->flush();  

            return $this->redirectToRoute('app_maison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('maison/new.html.twig', [
            'maison' => $maison,
            'form' => $form,
        ]);
    }


    #[Route('/{refB}', name: 'app_maison_show', methods: ['GET'])]
    public function show(Maison $maison): Response
    {
        return $this->render('maison/show.html.twig', [
            'maison' => $maison,
        ]);
    }

    #[Route('/{refB}/edit', name: 'app_maison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Maison $bien, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaisonType::class, $bien);
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
    
                $bien->setImage($newFilename);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_maison_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('maison/edit.html.twig', [
            'maison' => $bien,
            'form' => $form,
        ]);
    }
    #[Route('/{refB}', name: 'app_maison_delete', methods: ['POST'])]
    public function delete(Request $request, Maison $maison, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maison->getRefB(), $request->request->get('_token'))) {
            $entityManager->remove($maison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_maison_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/front', name: 'app_maison_front')]
    public function front(): Response
    {
  
        $maison = $this->getDoctrine()->getRepository(Maison::class)->findAll();

        return $this->render('maison/front.html.twig', [
            'maisons' => $maison, 
        ]);
        
    }
    
    #[Route('/pdf', name: 'app_maison_pdf')]
    public function impressionPDF()
    {
        $maison = new Maison();
        $repo =$this->getDoctrine()->getRepository(Maison::class);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('maison/pdf.html.twig', [
            'title' => "Welcome to our PDF Test",
            
            'maisons'=> $repo->findAll(),
            'maison' => $maison,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("ListDesMaisons.pdf", [
            "Attachment" => true
        ]);
    }


/*
    #[Route('/maisons/recherche', name: 'recherche_par_nom')]
    public function rechercheParNom(Request $request): Response
    {
        $nom = $request->query->get('nom');


        if ($nom !== null) {

            $maisons = $this->getDoctrine()->getRepository(Maison::class)->findBy(['nom' => $nom]);
        } else {

            $maisons = $this->getDoctrine()->getRepository(Maison::class)->findAll();
        }

        return $this->render('maison/index.html.twig', [
            'maisons' => $maisons,
        ]);
    }
    */
   
}
