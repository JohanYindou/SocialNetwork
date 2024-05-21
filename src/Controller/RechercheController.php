<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\RechercheType;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function search(Request $request, PublicationRepository $publications, RechercheType $rechercheType, EntityManagerInterface $em,)
    {
        $searchForm = $this->createForm(RechercheType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchTerm = $searchForm->getData()['search_term'];
        }
        if($searchTerm === null){
            $publications = $em->getRepository(Publication::class)->findAll();
            return $this->render('recherche/index.html.twig', [
                'publications' => $publications,
                'searchTerm' => $searchTerm,
                'searchForm' => $searchForm->createView(),
            ]);
        }
        else{
            // Retrieve filtered publications
            $filteredPublications = $publications->findBySearchTerm($searchTerm);

            return $this->render('recherche/index.html.twig', [
                'publications' => $filteredPublications,
                'searchTerm' => $searchTerm,
                'searchForm' => $searchForm->createView(),
            ]);
        }

    }
}