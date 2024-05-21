<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RechercheType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        // Response $response,
        PublicationRepository $publications,
        EntityManagerInterface $em,
        RouterInterface $router,
        Request $request,
    ): Response
    {
        $searchForm = $this->createForm(RechercheType::class);
        $searchForm->handleRequest($request);

        $searchTerm = $request->query->get('searchTerm'); // Get search term from query parameter

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchTerm = $searchForm->getData()['search_term'];

            // Redirect to search page with search term as query parameter
            $searchUrl = $router->generate('app_recherche', ['searchTerm' => $searchTerm]);
            return new RedirectResponse($searchUrl);
        }

        $publications = $em->getRepository(Publication::class)->findAll();
        return $this->render('home/index.html.twig', [
            'publications' => $publications,
            'searchForm' => $searchForm->createView(),
        ]);
    }
}
