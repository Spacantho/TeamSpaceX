<?php

namespace App\Entity;

use App\Repository\EmailConfirmationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailConfirmationRepository::class)]
#[ORM\UniqueConstraint(name: 'email_confirmation_unique', columns: ['user_id', 'email_id'])]
class EmailConfirmation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"], inversedBy: 'emailConfirmations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: Email::class, cascade: ["persist"], inversedBy: 'emailConfirmations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Email $email;

    public function __toString(): string
    {
        return "confirmation {$this->email}";
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function setEmail(?Email $email): self
    {
        $this->email = $email;

        return $this;
    }

}
