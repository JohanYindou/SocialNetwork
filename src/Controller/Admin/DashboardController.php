<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Commentaire;
use App\Entity\Publication;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SocialNetwork')
            ->setFaviconPath('/uploads/medias/Logo_SocialNetwork.png')
            ->renderContentMaximized(); // Optionally maximize the content area
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('User Management');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);

        yield MenuItem::section('Content Management');
        yield MenuItem::linkToCrud('Posts', 'fa fa-file-alt', Publication::class);
        yield MenuItem::linkToCrud('Comments', 'fa fa-comments', Commentaire::class);

        yield MenuItem::section('Settings');
        yield MenuItem::linkToRoute('Back to website', 'fa fa-undo', 'app_home');
    }
}
