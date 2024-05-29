<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Commentaire;
use App\Form\PublicationType;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PublicationRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;

class PostsController extends AbstractController
{
    #[Route('/post/{id}', name: 'app_post')]
    public function post(
        Request $request,
        PublicationRepository $publications,
        EntityManagerInterface $em,
    ): Response {

        $publicationId = $request->attributes->get('id'); // Get the ID from the route parameter
        $publication = $publications->findById($publicationId); // Find the publication by ID
        
        if (!$publication) {
            throw $this->createNotFoundException('La publication n\'existe pas.');
        }
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setPublication($publication);
            $commentaire->setAuteur($this->getUser());
            $commentaire->setCreatedAt(new \DateTime());

            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('app_post', ['id' => $publicationId]);
        }

        return $this->render('posts/index.html.twig', [
            'publication' => $publication,
            'commentForm' => $form->createView(),
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

    #[Route('/post/edit/{id}', name: 'app_post_edit')]
    public function editPost(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        PublicationRepository $publicationRepository
    ): Response {
        $publication = $publicationRepository->findById($id);

        if (!$publication || $publication->getAuteur() !== $this->getUser()) {
            throw $this->createNotFoundException('Publication non trouvée ou vous n\'êtes pas autorisé à modifier cette publication.');
        }

        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $form->get('contenu')->getData();
            $publication->setContenu($contenu)
                        ->setUpdatedAt(new \DateTime());
                        
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
                    $publication->setMedia('/uploads/medias/' . $newFilename);
                }
            }   
            $entityManager->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('posts/edit-post.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/post/delete/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function deletePost(
        int $id,
        EntityManagerInterface $entityManager,
        PublicationRepository $publicationRepository
    ): Response {
        $publication = $publicationRepository->findById($id);

        if (!$publication || $publication->getAuteur() !== $this->getUser()) {
            throw $this->createNotFoundException('Publication non trouvée ou vous n\'êtes pas autorisé à supprimer cette publication.');
        }

        $entityManager->remove($publication);
        $entityManager->flush();

        return $this->redirectToRoute('app_profil');
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

