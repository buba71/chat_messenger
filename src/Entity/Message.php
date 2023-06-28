<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('body_message')]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Groups('body_message')]
    private String $content;

    #[ORM\Column(type: "datetime")]
    #[Groups('body_message')]
    private \DateTime $createdAt;

    #[ORM\Column(type: "boolean")]
    #[Groups('body_message')]
    private bool $mine;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "messages")]
    private User $user;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Conversation", inversedBy: "messages")]
    private Conversation $conversation;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMine(): bool
    {
        return $this->mine;
    }

    public function setMine(bool $mine): void
    {
        $this->mine = $mine;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }
}
