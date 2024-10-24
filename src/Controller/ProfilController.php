<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Form\EditProfilType;
use App\Repository\CommentaireRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }

        return $this->render('profil/index.html.twig', [
        ]);
    }

    #[Route('/profil/comments', name: 'app_profil_comments')]
    public function profilComments(): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $comments= $user->getCommentaires();
        // dd($comments);
        return $this->render('profil/comments/profil_comments.html.twig', [
            'comments' => $comments
        ]);
    }

    #[Route('/comment/delete/{id}', name : 'app_profil_delete_comments', methods: ['POST'])]
    public function deleteComment(
        int $id,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        CommentaireRepository $commentaireRepository,
    ): Response
    {
        $commentaire = $commentaireRepository->findById($id);

        if (!$commentaire || $commentaire->getAuteur() !== $this->getUser()) {
            throw $this->createNotFoundException('Commentaire non trouvé ou vous n\'êtes pas autorisé à modifier ce commentaire.');
        }
        $entityManager->remove($commentaire);
        $entityManager->flush();
        return $this->redirectToRoute('app_profil_comments');
    }

    #[Route('/comment/edit/{id}', name :'app_profil-edit_comments',)]
    public function editComment(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        CommentaireRepository $commentaireRepository
    ): Response {
        
        $commentaire = $commentaireRepository->findById($id);

        if(!$commentaire || $commentaire->getAuteur() !== $this->getUser()){
            throw $this->createNotFoundException('Commentaire non trouvé ou vous n\'êtes pas autorisé à modifier ce commentaire.');
        }
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $form->get('contenu')->getData();
            $commentaire->setContenu($contenu)
                        ->setUpdatedAt(new \DateTime());
            $entityManager->flush();
            return $this->redirectToRoute('app_profil_comments');
        }

        return $this->render('profil/comments/profil_comments_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/likes', name: 'app_profil_likes')]
    public function profilLikes(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
    ): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $likedCommentaires = $user->getLikedCommentaires($user);
        $likedPublications = $user->getLikedPublications($user);

        return $this->render('profil/likes/profil_likes.html.twig', [
            'commentaires'=> $likedCommentaires,
            'publications' => $likedPublications
        ]);
    }


    #[Route('/profil/{id}', name: 'app_user_profil')]
    public function userProfile(
        UserRepository $userRepository,
        int $id
    ) : Response
    {

        $user = $userRepository->findById($id);
        if(!$user){
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('profil/user-profil.html.twig',[
            'user' => $user,
        ]);
    }

    #[Route('/profile/{id}/comments', name:'app_user_profil-comments')]
    public function userComments(
        UserRepository $userRepository,
        CommentaireRepository $commentaireRepository,
        int $id
    ) : Response
    {

        $user = $userRepository->findById($id);
        if(!$user){
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $comments = $commentaireRepository->findBy(['auteur' => $user]);
        return $this->render('profil/comments/user-profil_comments.html.twig', [
            'user' => $user,
            'comments' => $comments,
        ]);
    }
    
    #[Route('/profile/{id}/likes', name:'app_user_profil-likes')]
    public function userLikes(
        UserRepository $userRepository,
        int $id
    ) : Response
    {

        $user = $userRepository->findById($id);
        if(!$user){
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('profil/likes/user-profil_likes.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/profil-edit', name: 'app_profil_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
    ): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }

        $editProfilForm = $this->createForm(EditProfilType::class, $user);
        $editProfilForm->handleRequest($request);

        if ($editProfilForm->isSubmitted() && $editProfilForm->isValid()) {
            $user = $editProfilForm->getData();

            $user->setNom($editProfilForm->get('nom')->getData());
            $user->setUsername($editProfilForm->get('username')->getData());
            $user->setDescription($editProfilForm->get('description')->getData());

            if ($editProfilForm->get('profilePicture')->getData()) {
                $file = $editProfilForm->get('profilePicture')->getData();

                if (!in_array($file->getMimeType(), ['image/png', 'image/jpeg', 'image/gif'])) {
                    $editProfilForm->addError(new FormError('Le fichier téléchargé doit être une image (png, jpeg, gif).'));
                    return $this->render('profil/edit-profil.html.twig', [
                        'editProfilForm' => $editProfilForm->createView(),
                    ]);
                }

                // Check file size (assuming 2MB limit)
                if ($file->getSize() > 2048 * 1024) { // 2MB = 2 * 1024 KB
                    $editProfilForm->addError(new FormError('La taille de l\'image ne doit pas dépasser 2 Mo'));
                    return $this->render('profil/edit-profil.html.twig', [
                        'editProfilForm' => $editProfilForm->createView(),
                    ]);
                }

                $originalFileName = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter('upload_profilePictures'),
                    $originalFileName
                );
                $user->setProfilPicture('/uploads/users/' . $originalFileName);
            }
            
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Votre profil a bien été mis à jour");
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit-profil.html.twig', [
            'editProfilForm' => $editProfilForm->createView(),
        ]);
    }
}
