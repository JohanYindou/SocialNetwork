<?php

namespace App\Controller;

use App\Form\RechercheType;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function search(Request $request, PublicationRepository $publications)
    {
        $searchTerm = $request->query->get('searchTerm'); // Get search term from query parameter

        // Retrieve filtered publications using the search term
        $filteredPublications = $publications->findBySearchTerm($searchTerm);

        return $this->render('recherche/index.html.twig', [
            'publications' => $filteredPublications, // Pass filtered publications to the template
            'searchTerm' => $searchTerm, // Pass search term for display
        ]);
    }
}