<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Notification;
use App\Entity\User;
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
    public function like(Publication $publication, EntityManagerInterface $entityManager): JsonResponse
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

            // Créer et persister une notification
            $notification = new Notification();
            $notification->setUser($publication->getAuteur());
            $notification->setType('like');
            $notification->setMessage($user->getUsername() . ' a liké votre post');
            $notification->setCreatedAt(new \DateTime());

            $entityManager->persist($notification);
        }

        $entityManager->persist($publication);
        $entityManager->flush();

        return new JsonResponse(['likes' => $publication->getLikes()]);
    }
}