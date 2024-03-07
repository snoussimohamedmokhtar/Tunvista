<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;



#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository , Request $request , PaginatorInterface $paginator): Response
    {
        
        $query = $reclamationRepository->findAll();
        // Handle search
        $searchQuery = $request->query->get('q');
        if (!empty($searchQuery)) {
             $query = $reclamationRepository->findByExampleField($searchQuery);
        }
        $reclamation = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Current page number
            5 // Number of items per page
        );
        if ($request->isXmlHttpRequest()) {
                $paginationHtml = $this->renderView('reclamation/_paginator.html.twig', ['reclamations' => $reclamation]);
                $contentHtml = $this->renderView('reclamation/reclist.html.twig', ['reclamations' => $reclamation]);
               // Log to verify if the controller enters this condition
                       $this->logger->info('Request is AJAX');
               return new JsonResponse([
                    'content' => $contentHtml,
                    'pagination' => $paginationHtml
                    ]);
       } else {
        // Log to verify if the controller enters this condition
       $this->logger->info('Request is not AJAX');
        }


        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamation,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $reclamation = new Reclamation();

    // Set the date attribute to the current date and time
    $reclamation->setDate(new \DateTime());

    // Set the default value for etat
    $reclamation->setEtat('waiting');

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Add success flash message
        $this->addFlash('success', 'Your reclamation has been successfully submitted.');

        return $this->redirectToRoute('app_reclamation_new', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('reclamation/new.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/reclamation/statistics", name="reclamation_statistics")
     */
    public function statistics(ReclamationRepository $reclamationRepository): Response
    {
        $claimStatistics = $reclamationRepository->getClaimPercentageByType();

        return $this->render('reclamation/statistics.html.twig', [
            'claimStatistics' => $claimStatistics,
        ]);
    }


  

}
