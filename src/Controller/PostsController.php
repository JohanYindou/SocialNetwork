<?php

namespace App\Controller;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/post/{id}', name: 'app_post')]
    public function post(
        Request $request,
        PublicationRepository $publications,
        EntityManagerInterface $em,
    ): Response {
        // // Récupérer l'utilisateur actuellement connecté
        // $currentUser = $this->getUser();

        // // Vérifier si l'utilisateur est authentifié
        // if (!$currentUser) {
        //     return $this->redirectToRoute('app_login');
        //     throw $this->createNotFoundException('Utilisateur non connecté');
        // }

        $publicationId = $request->attributes->get('id'); // Get the ID from the route parameter
        $publication = $publications->find($publicationId); // Find the publication by ID

        return $this->render('posts/index.html.twig', [
            'publication' => $publication, 
        ]);
    }


    #[Route('/post/new', name: 'app_new_post')]
    public function newPost(): Response
    {
        return $this->render('new-post.html.twig',[
            
        ]);
    }

    #[Route('/comment/{id}', name: 'app_comment')]
    public function comments(
        Request $request,
        PublicationRepository $publications,
        EntityManagerInterface $em,
        CommentaireRepository $comments,
    ): Response {
        $comment = $comments->find($request->attributes->get('id'));
        // Récupérer l'id de la route du commentaire  et l'afficher
        return $this->render('posts/comment.html.twig', [
            'comment' => $comment,
        ]);
    }
}

