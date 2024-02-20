<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/Users',name:'admin_users_')]
class UsersController extends AbstractController
{

    #[Route('/',name: 'index')]
    public function index(UserRepository $userRepository):Response{
        $users = $userRepository->findBy( [] ,[
            'firstName' => 'asc'
        ]);
        return $this->render('admin/users/index.html.twig', compact('users'));
    }
}