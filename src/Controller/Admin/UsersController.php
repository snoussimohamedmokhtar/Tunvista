<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserFormType;
use App\Form\UserUpdateFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin/Users',name:'admin_users_')]
class UsersController extends AbstractController
{

//    #[Route('/', name: 'index')]
//    public function index(UserRepository $userRepository): Response
//    {
//        $users = $userRepository->findBy([], [
//            'firstName' => 'asc'
//        ]);
//        return $this->render('admin/users/index.html.twig', compact('users'));
//    }

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