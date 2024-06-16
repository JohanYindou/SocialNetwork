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
    private $messagesController;
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessagesController $messagesController
    ) {
        $this->messagesController = $messagesController;
        $this->entityManager = $entityManager;
    }

    #[Route('/conversation/create', name: 'conversation_create')]
    public function create(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
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
    public function index(
        ConversationRepository $conversationRepository
    ): Response {
        $user = $this->getUser();

        $conversations = $conversationRepository->getConversationsForUser($user);

        return $this->render('conversation/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }


    #[Route('/conversation/new', name: 'conversation_new')]
    public function new(
        UserRepository $userRepository
    ): Response {
        $users = $userRepository->findAllUsersExceptCurrent($this->getUser());

        return $this->render('conversation/create_conversation.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/conversation/{id}/add-participant', name: 'conversation_add_participant', methods: ['POST'])]
    public function addParticipant(
        Conversation $conversation,
        Request $request,
        UserRepository $userRepository,
    ): Response {
        $userId = $request->request->get('user_id');
        $participant = $userRepository->find($userId);

        if (!$participant) {
            throw $this->createNotFoundException('User not found');
        }

        if ($conversation->getParticipants()->contains($participant)) {
            $this->addFlash('error', 'User is already a participant in this conversation.');
            return $this->redirectToRoute('conversation_show', ['id' => $conversation->getId()]);
        }

        $conversation->addParticipant($participant);

        $this->entityManager->persist($conversation);
        $this->entityManager->flush();

        // Ajouter le message système après avoir ajouté le participant
        $content = sprintf('%s a été ajouté à la conversation.', $participant->getUsername());

        // Récupérer l'utilisateur système
        $systemUser = $userRepository->findOneBy(['username' => 'system']);
        if (!$systemUser) {
            throw $this->createNotFoundException('System user not found');
        }

        $this->messagesController->createSystemMessage($conversation, $content, $systemUser);

        return $this->redirectToRoute('conversation_show', ['id' => $conversation->getId()]);
    }
}
