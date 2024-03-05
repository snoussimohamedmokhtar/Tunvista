<?php

namespace App\Controller;

use App\Entity\Notifa;
use App\Repository\NotifRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotifaController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications_index', methods: ['GET'])]
    public function index(NotifRepository $notifaRepository): Response
    {
        $user = $this->getUser(); // Assuming you have a method to get the current user

        // Retrieve notifications for the current user
        $notifications = $notifaRepository->findBy(['user' => $user]);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notifications/{id}', name: 'app_notifications_show', methods: ['GET'])]
    public function show(Notifa $notifa): Response
    {
        // Render the notification details
        return $this->render('notifa/show.html.twig', [
            'notification' => $notifa,
        ]);
    }

    #[Route('/notifications/{id}', name: 'app_notifications_delete', methods: ['DELETE'])]
    public function delete(Request $request, Notifa $notifa): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notifa->getIdNotifa(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($notifa);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_notifications_index');
    }
}
