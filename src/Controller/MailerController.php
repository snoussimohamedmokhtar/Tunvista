<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MailerController extends AbstractController
{
    #[Route('visit/sendmail/{id}', name: 'mailing',methods: ['GET'])]
    public function sendEmail(Request $request , MailerInterface $mailer): Response
    {

        $email=$request->request->get('email');

        $email1 = (new Email())
            ->from($email)
            ->to($email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Confirmation visite')
            ->text('Sending emails is fun again!')
            ->html('<p>Bonjour , votre demande de visite  est  bien enregistré:</p>');
    
        $mailer->send($email1);
    
        return new Response('Email sent !');
    }
}