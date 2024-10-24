<?php

namespace App\Controller;

use App\Model\SearchData;
use App\Form\RechercheType;
use App\Repository\PublicationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function search(
        Request $request, 
        PublicationRepository $publicationRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        
        $searchData = New SearchData(); 
        $form = $this->createForm(RechercheType::class, $searchData);
        $form->handleRequest($request);
        $totalResults = 0;

        $pagination = $paginator->paginate(
            [], // ou une valeur par défaut appropriée
            $request->query->getInt('page', 1),
            5
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $results = $publicationRepository->findBySearchTerm($searchData->q);
            $totalResults = count($results);
            
            $pagination = $paginator->paginate(
                $publicationRepository->findBySearchTerm($searchData->q),
                $request->query->getInt('page', 1),
                5
            );
        }

        return $this->render('recherche/index.html.twig', [
            'publications' => $pagination,
            'searchData' => $searchData->q,
            'totalResults' => $totalResults,
            'form' => $form->createView(),
        ]);
    }
}
