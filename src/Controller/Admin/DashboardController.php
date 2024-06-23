<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Header;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {


        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Menu Administration');
            
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users-gear', User::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-swatchbook', Category::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-car-on', Product::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-truck-fast', Carrier::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-boxes-packing', Order::class);
        yield MenuItem::linkToCrud('Header', 'fas fa-images', Header::class);
        yield MenuItem::linkToUrl('Retour au site', 'fas fa-home', '/');
    }
}
