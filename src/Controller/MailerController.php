<?php

namespace App\Controller;

use App\Entity\Voyageur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('voyageur/sendmail/{id}', name: 'mailing',methods: ['GET'])]
    public function sendEmail(MailerInterface $mailer, $id): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $voyageur = $entityManager->getRepository(Voyageur::class)->find($id);

        if (!$voyageur) {
            throw $this->createNotFoundException('event not found for id ' . $id);
        }


        $emailContent = "Bonjour ,\n\n";
        $emailContent .= "Nous vous informons des détails concernant vous mr/mrs '" . $voyageur->getNom() . "'.\n";
        $emailContent .= "prenom: " . $voyageur->getPrenom() . "\n";
        $emailContent .= "age: " . $voyageur->getAge() . "\n";
        $emailContent .= "votre etat civil: " . $voyageur->getEtatCivil(). "\n";
        $emailContent .= "your email: " . $voyageur->getEmail(). "\n";
        $emailContent .= "your passport number: " . $voyageur->getNumPass(). "\n";

        $emailContent .= "Si vous avez des questions , n'hésitez pas à me contacter.\n";
        $emailContent .= "Cordialement,\n";

        $email = (new Email())
            ->from('kharrat.raed@esprit.tn')
            ->to($voyageur->getEmail())

            ->subject('Confirmation voyage')
            ->text('Sending emails is fun again!')
            ->text($emailContent);
    
        $mailer->send($email);
    
        // Return a response, for example, a simple acknowledgment message.
        return new Response('Email sent successfully');
    }
}


