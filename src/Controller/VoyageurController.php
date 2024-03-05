<?php

namespace App\Controller;

use App\Entity\Voyageur;
use App\Form\VoyageurType;
use App\Repository\VoyageurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/voyageur')]
class VoyageurController extends AbstractController
{

    #[Route('/stats', name: 'app_stat', methods: ['GET'])]
    public function statistics(VoyageurRepository $voyageurRepository): Response
    {
        $repository = $this->getDoctrine()->getRepository(Voyageur::class);

        $data = $repository->createQueryBuilder('v')
            ->select('v.EtatCivil')
            ->addSelect('COUNT(v.id) as totalEtatCivil')

            ->addSelect('SUM(CASE WHEN v.EtatCivil = :Single THEN 1 ELSE 0 END) as SingleCount')
            ->addSelect('SUM(CASE WHEN v.EtatCivil = :Married THEN 1 ELSE 0 END) as MarriedCount')
            ->addSelect('SUM(CASE WHEN v.EtatCivil = :Divorced THEN 1 ELSE 0 END) as DivorcedCount')
            ->addSelect('SUM(CASE WHEN v.EtatCivil = :Widowed THEN 1 ELSE 0 END) as WidowedCount')

            ->setParameter('Single', 'Single')
            ->setParameter('Married', 'Married')
            ->setParameter('Divorced', 'Divorced')
            ->setParameter('Widowed', 'Widowed')

            ->groupBy('v.EtatCivil')
            ->getQuery()
            ->getResult();

        return $this->render('voyageur/chart.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/', name: 'app_voyageur_index', methods: ['GET'])]
    public function index(VoyageurRepository $voyageurRepository): Response
    {
        return $this->render('voyageur/index.html.twig', [
            'voyageurs' => $voyageurRepository->findAll(),
        ]);
    }

    #[Route('/search', name: 'app_search_voyageur', methods: ['POST'])]
    public function searchAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->request->get('query'); // Get the search query from the form input

        // Create a DQL query to filter reclamations based on recText
        $dql = "SELECT v FROM App\Entity\Voyageur v WHERE v.Nom LIKE :query OR v.Prenom LIKE :query";

        // Execute the DQL query
        $voyageurs = $entityManager->createQuery($dql)
            ->setParameter('query', '%' . $query . '%')
            ->getResult();

        // Render the 'index.html.twig' template with the filtered reclamations
        return $this->render('Voyageur/index.html.twig', [
            'voyageurs' => $voyageurs,
        ]);
    }

    #[Route('/trieasc', name: 'app_trieasc', methods: ['GET'])]
    public function ascendingAction(VoyageurRepository $voyageurRepository)
    {
        return $this->render('voyageur/index.html.twig', [
            'voyageurs' => $voyageurRepository->findAllAscending("v.EtatCivil"),
        ]);
    }

    #[Route('/triedesc', name: 'app_triedesc', methods: ['GET'])]
    public function descendingAction(VoyageurRepository $voyageurRepository)
    {

        return $this->render('voyageur/index.html.twig', [
            'voyageurs' => $voyageurRepository->findAllDescending("v.EtatCivil"),
        ]);

    }

    #[Route('/new', name: 'app_voyageur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voyageur = new Voyageur();
        $form = $this->createForm(VoyageurType::class, $voyageur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($voyageur);
            $entityManager->flush();

            return $this->redirectToRoute('app_voyageur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyageur/new.html.twig', [
            'voyageur' => $voyageur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_voyageur_show', methods: ['GET'])]
    public function show(Voyageur $voyageur): Response
    {
        return $this->render('voyageur/show.html.twig', [
            'voyageur' => $voyageur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_voyageur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voyageur $voyageur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoyageurType::class, $voyageur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_voyageur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyageur/edit.html.twig', [
            'voyageur' => $voyageur,
            'form' => $form,
        ]);
    }

    #[Route('del/{id}', name: 'app_voyageur_delete', methods: ['POST'])]
    public function delete(Request $request, Voyageur $voyageur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voyageur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($voyageur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_voyageur_index', [], Response::HTTP_SEE_OTHER);
    }




}
