<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\RechercheType;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function search(Request $request, PublicationRepository $publicationRepository): Response
    {
        $searchForm = $this->createForm(RechercheType::class);
        $searchForm->handleRequest($request);

        $publications = [];
        $searchTerm = '';

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchTerm = $searchForm->get('search_term')->getData();
            $publications = $publicationRepository->findBySearchTerm($searchTerm);
        }

        return $this->render('recherche/index.html.twig', [
            'publications' => $publications,
            'searchTerm' => $searchTerm,
            'searchForm' => $searchForm->createView(),
        ]);
    }
}
