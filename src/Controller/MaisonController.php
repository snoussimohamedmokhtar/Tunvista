<?php

namespace App\Controller;

use App\Entity\Maison;
use App\Form\MaisonType;
use App\Repository\MaisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/maison')]
class MaisonController extends AbstractController
{
    #[Route('/', name: 'app_maison_index', methods: ['GET'])]
    public function index(MaisonRepository $maisonRepository): Response
    {
        return $this->render('maison/index.html.twig', [
            'maisons' => $maisonRepository->findAll(),
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
                    //deplacer le fichier vers un répertoire
                    $image->move(
                        $this->getParameter('image_directory'), // Chemin vers le répertoire de stockage des documents
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur de téléchargement de fichier
                }
    
                // Stockez le nom du fichier dans l'entité 
                $maison->setImage($newFilename);
            }
    
            $entityManager->persist($maison); // Enregistre l'objet $mecanicien dans le gestionnaire d'entités
            $entityManager->flush(); //Exécute réellement l'opération de persistance en base de données. 

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
                    // Gérer les erreurs liées au chargement de l'image
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
}
