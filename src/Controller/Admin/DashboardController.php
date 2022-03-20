<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Contrat;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct( private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(ContratCrudController::class)
        ->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
         return Dashboard::new()
            ->setTitle('Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::section('Dashboard Vershtapen');
        yield MenuItem::section('Voitures');
        yield MenuItem::subMenu('Contrats', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer un contrat', 'fas fa-plus', Contrat::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Mes contrats', 'fas fa-eye', Contrat::class)
        ]);

        yield MenuItem::subMenu('Categories', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Créer une catégorie', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Mes catégories', 'fas fa-eye', Category::class)
        ]);


    }
}
