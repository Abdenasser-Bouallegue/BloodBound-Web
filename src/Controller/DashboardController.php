<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Event;
use App\Entity\Ticket;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\TicketRepository;
use App\Repository\EventRepository;
use App\Repository\ArticleRepository;

class DashboardController extends AbstractController
{
    #[Route('/dash', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository,ProductRepository $productRepository ,EventRepository $eventRepository , ArticleRepository $articleRepository): Response
    {   
        $user = $this->getUser();
        $numUsers = $userRepository->count([]);
        $numProducts = $productRepository->count([]);
        $numEvents = $eventRepository->count([]);
        $numArticles = $articleRepository->count([]);
        
        return $this->render('BackOffice/dashboard.html.twig', [
            'user' => $user,
            'numUsers' => $numUsers,
            'numProducts' => $numProducts,
            'numEvents' => $numEvents,
            'numArticles' => $numArticles,
            
        ]);
    }

}
