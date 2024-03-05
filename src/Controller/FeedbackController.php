<?php
namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    #[Route('/feedback', name: 'app_feedback', methods: ['GET','POST'])]
public function index(Request $request): Response
{
$feedback = new Feedback();
$form = $this->createForm(FeedbackType::class, $feedback);
$feedback->setCreatedAt(new \DateTime());
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
$entityManager = $this->getDoctrine()->getManager();
$entityManager->persist($feedback);
$entityManager->flush();

$this->addFlash('success', 'Feedback submitted successfully!');

// Redirect or do something else after successful submission
return $this->redirectToRoute('app_annonce_index');
}

return $this->render('feedback/index.html.twig', [
'form' => $form->createView(),
]);
}
}
