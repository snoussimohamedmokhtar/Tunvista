<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hotel;
use App\Entity\Reservation;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaiementController extends AbstractController
{
    #[Route('/paiement/{id}', name: 'app_paiement')]
    public function index($id, EntityManagerInterface $em): Response
    {
        $hotel = $em->getRepository(Hotel::class)->find($id);

        // Vous pouvez également ajouter ici la logique pour récupérer les informations sur la réservation

        return $this->render('paiement/Pindex.html.twig', [
            'controller_name' => 'PaiementController',
            'hotel' => $hotel,
        ]);
    }

    #[Route('/checkout/{id}', name: 'app_checkout')]
    public function checkout($id, EntityManagerInterface $em): Response
    {
        Stripe::setApiKey('sk_test_51OqgDeBLgOBp2CJ7ampNPipL9yCv3vvR3zvnsIdCda7p4A4tjkLOCuCX5DWNXPuE4B02Xgmom2w85y29EgUU0dHX00qbkhgTuu');

        $hotel = $em->getRepository(Hotel::class)->find($id);

        // Vous pouvez ajouter ici la logique pour créer une réservation et enregistrer dans la base de données

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'USD',
                        'product_data' => [
                            'name' => 'Montant :', // Modifier le nom en fonction de vos besoins
                        ],
                        'unit_amount'  => $hotel->getPrixNuit() * 100, // Utilisez le prix de la nuit de l'hôtel pour le montant
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(SessionInterface $session1, EntityManagerInterface $entityManager): Response
    {
        return $this->render('paiement/success.html.twig', []);
    }

    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('paiement/cancel.html.twig', []);
    }
}
