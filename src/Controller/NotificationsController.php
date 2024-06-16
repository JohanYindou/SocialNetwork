<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationsController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function index(NotificationRepository $notificationRepository)
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(['user' => $user, 'isRead' => false]);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notifications/mark-as-read', name: 'notifications_mark_as_read')]
    public function markAsRead(NotificationRepository $notificationRepository, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(['user' => $user, 'isRead' => false]);

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }

        $entityManager->flush();

        return $this->redirectToRoute('notifications');
    }
    
    // A mettre dans la vue pour marqué comme lu
    // <a href="{{ path('notifications_mark_as_read') }}" class="btn btn-primary mb-3">Marquer tout comme lu</a>
}
