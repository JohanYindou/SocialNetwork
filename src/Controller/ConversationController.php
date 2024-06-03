<?php

// src/Controller/ConversationController.php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/conversations', name: 'conversation_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $conversations = $entityManager->getRepository(Conversation::class)->findAll();
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
