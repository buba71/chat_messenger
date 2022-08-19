<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Participant;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conversations', name: 'conversations')]
class ConversationController extends AbstractController
{
    public function __construct(
        private ConversationRepository $conversationRepository,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {}

    /**
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    #[Route('/{id}', name: 'getConversation')]
    public function index(Request $request, User $user): JsonResponse
    {
        $otherUser = $request->get('otherUser', 0);
        $otherUser = $this->userRepository->find($user->getId());

        if (is_null($otherUser)) {
            throw new EntityNotFoundException('The user does not exist');
        }

        // Cannot create a conversation with myself.
        if ($otherUser->getId() === $this->getUser()->getId()) {
            throw new \Exception('Sorry, but you cannot create a conversation with yourself.');
        }

        // Check if conversation already exist.
        $conversation = $this->conversationRepository->findConversationByParticipants(
            $otherUser->getId(),
            $this->getUser()->getId()
        );

        if (count($conversation)) {
            throw new \Exception('Conversation already exist.');
        }

        $conversation = new Conversation();

        $participant = new Participant();
        $participant->setUser($this->getUser());
        $participant->setConversation($conversation);

        $otherParticipant = new Participant();
        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->entityManager->persist($conversation);
            $this->entityManager->persist($participant);
            $this->entityManager->persist($otherParticipant);

            $this->entityManager->flush();
            $this->entityManager->commit();

        } catch (\Exception $exception){
            $this->entityManager->rollback();
            throw $exception;
        }

        return new JsonResponse(['id' => $conversation->getId()], Response::HTTP_CREATED);
    }


}