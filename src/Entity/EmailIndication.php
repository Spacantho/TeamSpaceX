<?php

namespace App\Entity;

use App\Repository\EmailIndicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailIndicationRepository::class)]
#[ORM\UniqueConstraint(name: 'indication_email_unique', columns: ['user_id', 'email_id'])]
class EmailIndication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    public function __construct()
    {
        $this->setDateCreation( new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: 'datetimetz_immutable')]
    private $date_creation;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"], inversedBy: 'emailIndications')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Email::class, cascade: ["persist"], inversedBy: 'emailIndications')]
    #[ORM\JoinColumn(nullable: false)]
    private $email;

  

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeImmutable $date_creation): self
    {
        $this->date_creation = $date_creation;

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
