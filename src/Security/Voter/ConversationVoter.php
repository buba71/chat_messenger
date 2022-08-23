<?php

namespace App\Security\Voter;

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConversationVoter extends Voter
{
    const VIEW = 'view';

    public function __construct(private ConversationRepository $conversationRepository) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Conversation;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $result = $this->conversationRepository->checkIfUserIsParticipant(
            $subject->getId(),
            $token->getUser()->getId()
        );

        return !!$result;
    }
}