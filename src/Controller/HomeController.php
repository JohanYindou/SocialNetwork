<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        // Response $response,
        PublicationRepository $publications,
        EntityManagerInterface $em,
    ): Response
    {
        $publications = $em->getRepository(Publication::class)->findAll();
        return $this->render('home/index.html.twig', [
            'publications' => $publications,
        ]);
    }
}
