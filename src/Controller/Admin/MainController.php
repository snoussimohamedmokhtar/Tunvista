<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted("ROLE_ADMIN")]
#[Route('/admin',name:'admin_')]
class MainController extends AbstractController
{

    #[Route('/',name: 'index')]
    public function index():Response{
        return $this->render('admin/index.html.twig');
    }
}