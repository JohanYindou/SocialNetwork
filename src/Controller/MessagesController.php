<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSystemMessage(
        Conversation $conversation,
        string $content,
        User $systemUser
    ): void {
        $message = new Message();
        $message->setContenu($content);
        $message->setCreatedAt(new \DateTime());
        $message->setConversation($conversation);
        $message->setUtilisateur($systemUser);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    #[Route('/conversation/{id}', name: 'conversation_show')]
    public function show(Conversation $conversation, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $messages = $entityManager->getRepository(Message::class)
            ->findBy(['conversation' => $conversation]);

        $participants = $conversation->getParticipantsExceptCurrent($this->getUser());
        $allUsers = $userRepository->findAllUsersExceptCurrentAndParticipants($this->getUser(), $conversation);

        return $this->render('conversation/show.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages,
            'participants' => $participants,
            'all_users' => $allUsers,
        ]);
    }

    #[Route('/message/send', name: 'app_message', methods: ['POST'])]
    public function send(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $conversationId = $request->request->get('conversation_id');
        $contenu = $request->request->get('contenu');

        if (!$conversationId) {
            throw $this->createNotFoundException('Conversation not found');
        }
        $conversation = $entityManager->getRepository(Conversation::class)->find($conversationId);

        if (!$conversation) {
            throw $this->createNotFoundException('Conversation not found');
        }

        $message = new Message();
        $message->setContenu($contenu);
        $message->setCreatedAt(new \DateTime());
        // $message->setStatus(true); // ou false, selon votre logique
        $message->setConversation($conversation);
        $message->setUtilisateur($this->getUser());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->redirectToRoute('conversation_show', ['id' => $conversationId]);
    }
}
