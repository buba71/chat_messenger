<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/messages', name: 'messages')]
class MessageController extends AbstractController
{
    public const ATTRIBUTES_TO_SERIALIZE = ['id', 'content', 'createdAt', 'mine'];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository $messageRepository,
        private NormalizerInterface $normalizer,
        private UserRepository $userRepository
    ) {}

    #[Route('/{id}', name: 'getMessages', methods: ['GET'])]
    public function getMessages(Request $request, Conversation $conversation): JsonResponse
    {
        // Can current user view the conversation ?
        $this->denyAccessUnlessGranted('view', $conversation);

        $messages = $this->messageRepository->getMessagesByConversationID($conversation->getId());

        array_map(fn($message) =>
            $message->setMine(
                $message->getUser()->getId() === $this->getUser()->getId()
            )
        , $messages);

        $messages = $this->normalizer->normalize($messages, null, ['groups' => 'body_message']);

        return new  JsonResponse($messages, Response::HTTP_OK, []);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    #[Route('/{id}', name: 'newMessage', methods: ['POST'])]
    public function newMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $this->getUser();
        $content = $request->get('content', null);

        $message = new Message();
        $message->setContent($content);
        $message->setUser($user);
        $message->setMine(true);
        
        $conversation->addMessage($message);
        $conversation->setLastMessage($message);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->entityManager->persist($message);
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();

            $this->entityManager->commit();

        } catch(\Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        $message = $this->normalizer->normalize($message, null, ['groups' => 'body_message']);
        return new JsonResponse($message, Response::HTTP_CREATED);
    }
}