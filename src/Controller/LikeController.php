<?php

namespace App\Controller;

use App\Entity\Publication;
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

        if ($publication->getLikedBy()->contains($user)) {
            $publication->removeLikedBy($user);
            $publication->decrementLikes();
        } else {
            $publication->addLikedBy($user);
            $publication->incrementLikes();
        }

        $entityManager->persist($publication);
        $entityManager->flush();

        return new JsonResponse(['likes' => $publication->getLikes()]);
    }
}