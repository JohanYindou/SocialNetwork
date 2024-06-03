<?php

// src/Controller/ConversationController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Conversation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConversationController extends AbstractController
{
    #[Route('/conversation/create', name: 'conversation_create')]
    public function create(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $userId = $request->request->get('user_id');
        $participant = $userRepository->find($userId);

        if (!$participant) {
            throw $this->createNotFoundException('User not found');
        }

        $conversation = new Conversation();
        $conversation->addParticipant($this->getUser());
        $conversation->addParticipant($participant);

        $entityManager->persist($conversation);
        $entityManager->flush();

        return $this->redirectToRoute('conversation_index');
    }

    #[Route('/messages', name: 'conversation_index')]
    public function index(ConversationRepository $conversationRepository): Response
    {
        $user = $this->getUser();

        $conversations = $conversationRepository->getConversationsForUser($user);

        return $this->render('conversation/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }


    #[Route('/conversation/new', name: 'conversation_new')]
    public function new(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllUsersExceptCurrent($this->getUser());

        return $this->render('conversation/create_conversation.html.twig', [
            'users' => $users,
        ]);
    }
}
