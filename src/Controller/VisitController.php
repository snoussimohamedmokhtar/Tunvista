<?php

namespace App\Controller;

use App\Entity\Visit;
use App\Form\VisitType;
use App\Repository\VisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MaisonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mime\Email;


#[Route('/visit')]
class VisitController extends AbstractController
{
    #[Route('/', name: 'app_visit_index', methods: ['GET'])]
    public function index(Request $request,MaisonRepository $maisonRepository ,VisitRepository $visitRepository,PaginatorInterface $paginator): Response
    {
        $maisons = $maisonRepository->findAll();
        $visits = $visitRepository->findAll();
       
        // Calculate visits per maison
        $visitsPerMaison = [];
        foreach ($maisons as $maison) {
            $maisonId = $maison->getRefB();
            $visitsPerMaison[$maisonId] = 0; // Initialize the count
        }
    
        foreach ($visits as $visit) {
            $maisonId = $visit->getRefB()->getRefB();
            $visitsPerMaison[$maisonId]++;
        }

    
        // Render the template with maisons, visits, and visitsPerMaison
        return $this->render('visit/index.html.twig', [
            'maisons' => $maisons,
            'visits' => $visits,
            'visitsPerMaison' => $visitsPerMaison,
        ]);
    }
  
     

    #[Route('/new', name: 'app_visit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,MailerInterface $mailer): Response
    {
        $visit = new Visit();
        $form = $this->createForm(VisitType::class, $visit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($visit);
            $entityManager->flush();
            $nom = $form->get('nom')->getData();
            $email1 = $form->get('email')->getData();
            dump($email1); // Check the value of $email1

            $email = (new Email())
            ->from('malekbdiri06@gmail.com')
            ->to($email1) 
            ->subject('Confirmation visite')
            ->text('Sending emails is fun again!')
            ->html('<p>Bonjour '.$nom.', votre demande de visite a bien été enregistrée </p>');
        
    
        $mailer->send($email);
    
        // Return a response, for example, a simple acknowledgment message.
        return new Response('Email sent !');

            return $this->redirectToRoute('app_visit_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('visit/new.html.twig', [
            'visit' => $visit,
            'form' => $form,
        ]);
    }

/*
#[Route('/new', name: 'app_visit_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $visit = new visit();
        $form = $this->createForm(VisitType::class, $visit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la date de visite soumise dans le formulaire
            $dateVisit = $visit->getDateVisit();

            // Vérifier la disponibilité dans la base de données
            $isDateAvailable = $this->getDoctrine()->getRepository(Visit::class)
                ->isDateAvailable($dateVisit);

            if (!$isDateAvailable) {
                // La date n'est pas disponible, afficher un message approprié
                $this->addFlash('danger', 'La date de visite est déjà réservée. Veuillez choisir une autre date.');
                return $this->redirectToRoute('visit_new');
            }

            // Si la date est disponible, poursuivre le traitement
        }

        return $this->render('visit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
*/
    #[Route('/{id}', name: 'app_visit_show', methods: ['GET'])]
    public function show(Visit $visit): Response
    {
        return $this->render('visit/show.html.twig', [
            'visit' => $visit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_visit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Visit $visit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VisitType::class, $visit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_visit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('visit/edit.html.twig', [
            'visit' => $visit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_visit_delete', methods: ['POST'])]
    public function delete(Request $request, Visit $visit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$visit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($visit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_visit_index', [], Response::HTTP_SEE_OTHER);
    }
}
