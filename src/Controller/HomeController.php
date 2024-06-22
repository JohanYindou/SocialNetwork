<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request, 
        PublicationRepository $publicationRepository,
        PaginatorInterface $paginator
        ): Response
    {

        $pagination = $paginator->paginate(
            $publicationRepository->findAllOrderedByDate(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('home/index.html.twig', [
            'publications' => $pagination,
        ]);
    }
}
