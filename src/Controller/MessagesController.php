<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagesController extends AbstractController
{

    #[Route('/conversation/{id}', name: 'conversation_show')]
    public function show(Conversation $conversation, EntityManagerInterface $entityManager): Response
    {
        $messages = $entityManager->getRepository(Message::class)
            ->findBy(['conversation' => $conversation]);

        return $this->render('conversation/show.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    #[Route('/message/send', name: 'app_message', methods:['POST'])]
    public function send(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
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
        $message->setStatus(true); // ou false, selon votre logique
        $message->setConversation($conversation);
        $message->setUtilisateur($this->getUser());

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->redirectToRoute('conversation_show', ['id' => $conversationId]);
    }
}
