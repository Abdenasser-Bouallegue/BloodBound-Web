<?php

namespace App\Controller\Admin;
use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ArticleCrudController;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class DashboardController extends AbstractDashboardController
{    
    
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
        )
    {
    
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->SetController(ArticleCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BloodBound');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
         yield MenuItem::subMenu('Articles', 'fas fa-newspaper')->SetSubItems([
            MenuItem::linkToCrud('Tous les articles','fas fa-newspaper',Article::class),
            MenuItem::linkToCrud('Ajouter','fas fa-plus', Article::class)->setAction(Crud::PAGE_NEW)
         ]);
    }
   

}
