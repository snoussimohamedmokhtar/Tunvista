<?php

// src/Controller/WeatherController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather', name: 'app_weather')]
    public function index(): Response
    {
        $apiKey = 'f9c6b940fe371fd2d6824cdf565e0a60';
        $city = 'Tunisia'; // ou n'importe quelle autre ville
       
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', "http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}");

        $data = $response->toArray();

        return $this->render('weather/index.html.twig', [
            'weatherData' => $data,
        ]);
    }
}