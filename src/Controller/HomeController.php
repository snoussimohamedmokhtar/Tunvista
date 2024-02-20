<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/locationVoiture', name: 'app_locationVoiture')]
    public function destination(): Response
    {
        return $this->render('locationVoiture.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/voyageOrganise', name: 'app_voyageOrganise')]
    public function voyageOrganise(): Response
    {
        return $this->render('voyageOrganise.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
