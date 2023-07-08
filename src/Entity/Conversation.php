<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Index(columns: ["last_message_id"], name: "last_message_id_index")]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('body_message')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: "App\Entity\Message")]
    #[ORM\JoinColumn(name: "last_message_id", referencedColumnName: "id")]
    private Message $lastMessage;

    #[ORM\OneToMany(mappedBy: "conversation", targetEntity: "App\Entity\Message")]
    private $messages;

    #[ORM\OneToMany(mappedBy: "conversation", targetEntity: "App\Entity\Participant")]
    private $participants;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setConversation($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getConversation() === $this) {
                $participant->setConversation(null);
            }
        }

        return $this;
    }
}
