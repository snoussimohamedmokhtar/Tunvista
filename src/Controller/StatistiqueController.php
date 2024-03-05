<?php

// src/Controller/StatistiqueController.php

namespace App\Controller;

use App\Repository\HotelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiqueController extends AbstractController
{
    #[Route('/statistique', name: 'app_statistique')]
    public function index(hotelRepository $hotelRepository): Response
    {
        $hotelStatistics = $hotelRepository->gethotelStatistics();

        $labels = [];
        $data = [];

        foreach ($hotelStatistics as $statistic) {
            $labels[] = $statistic['Nbre_etoile'];
            $data[] = $statistic['count'];
        }

        return $this->render('statistique/hotel.html.twig', [
            'labels' => json_encode($labels),
            'data' => json_encode($data),
        ]);
    }
  
}
