<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Entity\Notification;
use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class LikeController extends AbstractController
{
    private $security;
    private $em;
    private $csrfTokenManager;

    public function __construct(Security $security, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->security = $security;
        $this->em = $em;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/like/{id}', name: 'like_post', methods: ['POST'])]
    public function like(
        Publication $publication,
        EntityManagerInterface $entityManager, 
        NotificationService $notificationService): JsonResponse
    {
        $user = $this->security->getUser();

        // Vérification du type de l'utilisateur
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 403);
        }

        if ($publication->getLikedBy()->contains($user)) {
            $publication->removeLikedBy($user);
            $publication->decrementLikes();
        } else {
            $publication->addLikedBy($user);
            $publication->incrementLikes();

            $message = sprintf('%s a aimé votre publication.', $user->getUsername());
            $notificationService->createNotification($publication->getAuteur(), Notification::TYPE_LIKE, $message);
        }
        $entityManager->flush();

        return new JsonResponse(['likes' => $publication->getLikes()]);
    }

    #[Route('like/comment/{id}', 'like_comment', methods : ['POST'])]
    public function likeComment(
        Commentaire $commentaire,
        EntityManagerInterface $entityManager, 
        NotificationService $notificationService): JsonResponse
    {
        $user = $this->security->getUser();

        // Vérification du type de l'utilisateur
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 403);
        }

        if ($commentaire->getLikedBy()->contains($user)) {
            $commentaire->removeLikedBy($user);
            $commentaire->decrementLikes();
        } else {
            $commentaire->addLikedBy($user);
            $commentaire->incrementLikes();

            $message = sprintf('%s a aimé votre commentaire.', $user->getUsername());
            $notificationService->createNotification($commentaire->getAuteur(), Notification::TYPE_LIKE, $message);
        }

        $entityManager->flush();
        return new JsonResponse(['likes' => $commentaire->getLikes()]);
    }
}