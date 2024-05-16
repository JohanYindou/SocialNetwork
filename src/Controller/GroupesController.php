<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GroupesController extends AbstractController
{
    #[Route('/groupes', name: 'app_groupes')]
    public function index(): Response
    {
        return $this->render('groupes/index.html.twig', [
            'controller_name' => 'GroupesController',
        ]);
    }
}
