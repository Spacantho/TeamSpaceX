<?php

namespace App\Entity;

use App\Repository\EmailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailRepository::class)]
#[ORM\UniqueConstraint(columns: ['email'])]
class Email
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'email', targetEntity: EmailConfirmation::class)]
    private $emailConfirmations;

    #[ORM\OneToMany(mappedBy: 'email', targetEntity: EmailIndication::class, orphanRemoval: true)]
    private $emailIndications;

    public function __construct()
    {
        $this->emailConfirmations = new ArrayCollection();
        $this->emailIndications = new ArrayCollection();

    }

    public function __toString() {
        return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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

    /**
     * @return Collection<int, EmailConfirmation>
     */
    public function getEmailConfirmations(): Collection
    {
        return $this->emailConfirmations;
    }

    public function addEmailConfirmation(EmailConfirmation $emailConfirmation): self
    {
        if (!$this->emailConfirmations->contains($emailConfirmation)) {
            $this->emailConfirmations[] = $emailConfirmation;
            $emailConfirmation->setDescription($this);
        }

        return $this;
    }

    public function removeEmailConfirmation(EmailConfirmation $emailConfirmation): self
    {
        if ($this->emailConfirmations->removeElement($emailConfirmation)) {
            // set the owning side to null (unless already changed)
            if ($emailConfirmation->getDescription() === $this) {
                $emailConfirmation->setDescription(null);
            }
        }

        return $this;
    }

     /**
     * @return Collection<int, IndicatedEmail>
     */
    public function getEmailIndications(): Collection
    {
        return $this->indicatedEmails;
    }

    public function addEmailIndication(EmailIndication $emailIndication): self
    {
        if (!$this->emailIndications->contains($emailIndication)) {
            $this->emailIndications[] = $emailIndication;
            $emailIndication->setEmail($this);
        }

        return $this;
    }

    public function removeEmailIndication(EmailIndication $emailIndication): self
    {
        if ($this->emailIndications->removeElement($emailIndication)) {
            // set the owning side to null (unless already changed)
            if ($emailIndication->getEmail() === $this) {
                $emailIndication->setEmail(null);
            }
        }

        return $this;
    }


}
