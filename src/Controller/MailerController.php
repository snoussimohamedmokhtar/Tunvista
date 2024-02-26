<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('voyageur/sendmail/{id}', name: 'mailing',methods: ['GET'])]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('zevvbbejahzgeu@zehurgze')
            ->to('eya.ali@esprit.tn')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Confirmation voyage')
            ->text('Sending emails is fun again!')
            ->html('<p>hello voyageur, ur flight is well noted , thanks very nice :*</p>');
    
        $mailer->send($email);
    
        // Return a response, for example, a simple acknowledgment message.
        return new Response('Email sent successfully');
    }
}
