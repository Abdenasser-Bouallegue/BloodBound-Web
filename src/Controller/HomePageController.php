<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    #[Route('/home', name: 'app_home_page')]
    public function index(): Response
    {
        return $this->render('/FrontOffice/homePage.html.twig', [
            'home_page' => 'HomePageController',
        ]);
    }

    
}
