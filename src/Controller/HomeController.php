<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RechercheType;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, PublicationRepository $publicationRepository, EntityManagerInterface $em): Response
    {
        $searchForm = $this->createForm(RechercheType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchTerm = $searchForm->get('search_term')->getData();
            return $this->redirectToRoute('app_recherche', ['searchTerm' => $searchTerm]);
        }

        $publications = $publicationRepository->findAll();

        return $this->render('home/index.html.twig', [
            'publications' => $publications,
            'searchForm' => $searchForm->createView(),
        ]);
    }
}
