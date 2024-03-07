<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerificationController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/verify-email/{token}', name: 'app_verify_email')]
    public function verifyEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            // Handle invalid token
            throw $this->createNotFoundException('Invalid verification token');
        }

        // Verify the email
        $user->setIsVerified(true);
        $verificationToken = bin2hex(random_bytes(32));

        $user->setVerificationToken($verificationToken); // Remove the token after verification
        $entityManager->flush();

        // Optionally, render a template or return a response
        return $this->render('verification/email_verified.html.twig', [
            'user' => $user,
        ]);
    }
}