<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserFormType;
use App\Form\UserUpdateFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use TCPDF;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin/Users',name:'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/user-chart', name: 'user_chart')]
    public function userChart(UserRepository $userRepository): Response
    {
        // Récupérer les utilisateurs avec leurs régions
        $users = $userRepository->findAllWithRegion();

        // Compter le nombre d'utilisateurs par région
        $userCountByRegion = [];
        foreach ($users as $user) {
            $regionName = $user->getRegion() ? $user->getRegion()->getNom() : 'Unknown';
            if (!isset($userCountByRegion[$regionName])) {
                $userCountByRegion[$regionName] = 0;
            }
            $userCountByRegion[$regionName]++;
        }

        // Préparer les données pour le graphique
        $labels = [];
        $data = [];
        foreach ($userCountByRegion as $region => $count) {
            $labels[] = $region;
            $data[] = $count;
        }

        // Rendre la vue et transmettre les données
        return $this->render('admin/users/chart.html.twig', [
            'labels' => json_encode($labels), // Convertir en JSON pour JavaScript
            'data' => json_encode($data), // Convertir en JSON pour JavaScript
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Récupérer le paramètre de tri de la requête
        $sortBy = $request->query->get('sortBy', 'firstName');

        // Vérifier si la valeur passée dans $sortBy est valide
        $validSortOptions = ['firstName', 'lastName', 'email', 'id', 'adresse', 'region', 'ville'];
        if (!in_array($sortBy, $validSortOptions)) {
            throw $this->createNotFoundException('Invalid sort option.');
        }

        // Récupérer la direction de tri de la requête (ascendant ou descendant)
        $sortDirection = $request->query->get('sortDirection', 'asc');

        // Vérifier si la direction de tri est valide
        $validSortDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $validSortDirections)) {
            throw $this->createNotFoundException('Invalid sort direction.');
        }


        // Récupérer les données du formulaire de recherche
        $query = $request->request->get('query');

        // Utiliser le repository pour obtenir les utilisateurs triés et filtrés
        $users = $userRepository->findBySearchQuery($query, $sortBy, $sortDirection);

//        // Utiliser le repository pour obtenir les utilisateurs triés
//        $users = $userRepository->findBy([], [
//            $sortBy => $sortDirection,
//        ]);

        // Rendre la vue avec les utilisateurs triés
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/pdf', name: 'pdf')]
    public function generatePdf(): Response
    {
        // Récupérer les données des locations
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        // Créer un nouvel objet TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Définir le titre du document
        $pdf->SetTitle('Tunvista - Tableau Des Users');

        // Ajouter une page
        $pdf->AddPage();

        // Ajouter une image
        $image_file = 'img/Logo_ESPRIT_Ariana.jpg';
        $pdf->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Ajouter un titre coloré
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(255, 0, 0); // Couleur rouge
        $pdf->Cell(0, 10, 'Tunvista', 0, true, 'C');

        // Ajouter un sous-titre
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 0, 0); // Couleur noire
        $pdf->Cell(0, 10, 'Tableau Des Users', 0, true, 'C');

        // Initialiser $html en tant que tableau
        $html = [];

        // Créer le tableau des locations
        $html[] = '<table border="1" cellpadding="5" cellspacing="0">';
        $html[] = '<tr>';
        $html[] = '<th>ID</th>';
        $html[] = '<th>Email</th>';
        $html[] = '<th>Roles</th>';
        $html[] = '<th>Prénom</th>';
        $html[] = '<th>Nom</th>';
        $html[] = '<th>Adresse</th>';
        $html[] = '<th>Région</th>';
        $html[] = '<th>Ville</th>';
        $html[] = '</tr>';
        foreach ($users as $user) {
            $html[] = '<tr>';
            $html[] = '<td>' . $user->getId() . '</td>';
            $html[] = '<td>' . $user->getEmail() . '</td>';
            $html[] = '<td>' . implode(', ', $user->getRoles()) . '</td>';
            $html[] = '<td>' . $user->getFirstName() . '</td>';
            $html[] = '<td>' . $user->getLastName() . '</td>';
            $html[] = '<td>' . $user->getAdresse() . '</td>';
            $html[] = '<td>' . ($user->getRegion() ? $user->getRegion()->getNom() : '') . '</td>'; // Assurez-vous que Region a une méthode getName()
            $html[] = '<td>' . $user->getVille() . '</td>';
            $html[] = '</tr>';
        }
        $html[] = '</table>';

        // Écrire le contenu HTML dans le PDF
        $pdf->writeHTML(implode('', $html), true, false, true, false, '');

        // Envoyer le PDF en réponse
        return new Response($pdf->Output('tableau_Users.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="tableau_Users.pdf"',
        ]);
    }


    #[Route('/new', name: 'admin_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('admin_users_index');

        }
        return $this->render('admin/users/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_users_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur à éditer
        $user = $entityManager->getRepository(User::class)->find($id);
        $mapsLink = $user->getMapsLink();

        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Créer le formulaire et le gérer
        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($request);

        // Traiter le formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Rediriger vers la liste des utilisateurs après édition
            return $this->redirectToRoute('admin_users_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendre le formulaire et la vue d'édition
        return $this->renderForm('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'maps_link' => $mapsLink,
        ]);
    }
    #[Route('/{id}', name: 'admin_users_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Charger l'utilisateur à supprimer depuis la base de données
        $user = $entityManager->getRepository(User::class)->find($id);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Vérifier le jeton CSRF
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_users_index', [], Response::HTTP_SEE_OTHER);
    }


}