<?php

namespace App\Controller;

use App\Model\SearchData;
use App\Form\RechercheType;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function search(Request $request, PublicationRepository $publicationRepository): Response
    {
        $searchData = New SearchData(); 
        $form = $this->createForm(RechercheType::class, $searchData);
        $form->handleRequest($request);

        $publications = [];
        // $searchTerm = '';

        if ($form->isSubmitted() && $form->isValid()) {
            // $searchTerm = $searchForm->get('searchTerm')->getData();
            
            // dd($searchData->q);
            // dd($searchForm);
            $publications = $publicationRepository->findBySearchTerm($searchData->q);
        }

        return $this->render('recherche/index.html.twig', [
            'publications' => $publications,
            'searchData' => $searchData->q,
            'form' => $form->createView(),
        ]);
    }
}
