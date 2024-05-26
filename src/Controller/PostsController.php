<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
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


    #[Route('/new-post', name: 'app_new_post', methods: ['GET','POST'])]
    public function newPost(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publication->setCreatedAt(new \DateTime());
            $publication->setAuteur($this->getUser());
            $publication->setLikes(0);

            // Récupération des données du formulaire
            $contenu = $form->get('contenu')->getData();
            $publication->setContenu($contenu);

            $mediaFile = $form->get('media')->getData();
            if ($mediaFile) {
                if (!in_array($mediaFile->getMimeType(), ['image/png', 'image/jpeg', 'image/gif'])) {
                    $form->addError(new FormError('Le fichier téléchargé doit être une image (png, jpeg, gif).'));
                } elseif ($mediaFile->getSize() > 2048 * 1024) {
                    $form->addError(new FormError('La taille de l\'image ne doit pas dépasser 2 Mo'));
                } else {
                    $newFilename = $mediaFile->getClientOriginalName();
                    $mediaFile->move(
                        $this->getParameter('upload_medias'),
                        $newFilename
                    );
                    $publication->setMedia('/uploads/medias/'.$newFilename);
                }
            }

            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }
        return $this->render('posts/new-post.html.twig',[
            'form' => $form->createView(),
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

