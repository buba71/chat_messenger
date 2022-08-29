<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
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
        private HubInterface $hub,
        private MessageRepository $messageRepository,
        private NormalizerInterface $normalizer,
        private ParticipantRepository $participantRepository,
        private SerializerInterface $serializer,
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

        $user = $this->userRepository->findOneBy(['id' => 1]);
        $recipient = $this->participantRepository->findParticipantByConversationIdAndUser($conversation->getId(), $user->getId());

        $content = $request->get('content', null);

        $message = new Message();
        $message->setContent($content);
        $message->setUser($user);

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

        $message->setMine(false);
        $messageSerialized = $this->serializer->serialize($message, 'json', ['groups' => 'body_message']);


        $update = new Update(
            [
                sprintf("/conversations/%s", $conversation->getId()),
                sprintf("/conversations/%s", $recipient->getUser()->getUsername())
            ],
            $messageSerialized,
            false,
            sprintf("/%s", $recipient->getUser()->getUserName())
        );

        // dd($update);
        
        $this->hub->publish($update);

        $message->setMine(true);
        $messageNormalized = $this->normalizer->normalize($message, null, ['groups' => 'body_message']);

        return new JsonResponse($message, Response::HTTP_CREATED);
    }
}