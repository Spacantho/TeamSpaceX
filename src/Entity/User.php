<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['uuid'], message: 'Un compte avec cet uuid existe')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $uuid;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MotDePasse::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $motDePasses;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EmailConfirmation::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $emailConfirmations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EmailIndication::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $emailIndications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Pseudo::class, orphanRemoval: true)]
    private $pseudos;


    public function __construct()
    {
        $this->motDePasses = new ArrayCollection();
        $this->emailConfirmations = new ArrayCollection();
        $this->emailIndications = new ArrayCollection(); 

        $uuid = Uuid::v6();
        $this->setUuid($uuid);
        
        $this->pseudos = new ArrayCollection();
    }


    public function __toString(): string
    {
        if(!$this->getEmailConfirmations()->isEmpty())
        {
            return $this->getEmailConfirmations()->last()->getEmail()?->getEmail();
        }
        if(!$this->getEmailConfirmations()->isEmpty())
        {
            return $this->getEmailConfirmations()->last()->getEmail()?->getEmail();
        }
        return "Inconnu";
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uuid;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->getMotDePasses()->last()->getValeur();
    }

     /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return !$this->emailConfirmations->isEmpty();
    }


    /**
     * @return Collection<int, MotDePasse>
     */
    public function getMotDePasses(): Collection
    {
        return $this->motDePasses;
    }

    public function addMotDePass(MotDePasse $motDePass): self
    {
        if (!$this->motDePasses->contains($motDePass)) {
            $this->motDePasses[] = $motDePass;
            $motDePass->setUser($this);
        }

        return $this;
    }

    public function removeMotDePass(MotDePasse $motDePass): self
    {
        if ($this->motDePasses->removeElement($motDePass)) {
            // set the owning side to null (unless already changed)
            if ($motDePass->getUser() === $this) {
                $motDePass->setUser(null);
            }
        }

        return $this;
    }

    
    /**
     * renvoie le dernier mail renseigné
     */
    public function getEmail(): ?string
    {
        if(!$this->emailConfirmations->isEmpty()) {
            return (string)$this->getEmailConfirmations()->last()->getEmail();
        }

        if(!$this->emailIndications->isEmpty()) {
            return (string)$this->getEmailIndications()->last()->getEmail();
        }

        return null;
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
            $emailConfirmation->setUser($this);
        }

        return $this;
    }

    public function removeEmailConfirmation(EmailConfirmation $emailConfirmation): self
    {
        // set the owning side to null (unless already changed)
        if ($this->emailConfirmations->removeElement($emailConfirmation) && $emailConfirmation->getUser() === $this) {
            $emailConfirmation->setUser(null);
        }

        return $this;
    }


     /**
     * @return Collection<int, EmailIndication>
     */
    public function getEmailIndications(): Collection
    {
        return $this->emailIndications;
    }

    public function addEmailIndication(EmailIndication $emailIndication): self
    {
        if (!$this->emailIndications->contains($emailIndication)) {
            $this->emailIndications[] = $emailIndication;
            $emailIndication->setUser($this);
        }

        return $this;
    }

    public function removeEmailIndication(EmailIndication $emailIndication): self
    {
        // set the owning side to null (unless already changed)
        if ($this->emailIndications->removeElement($emailIndication) && $emailIndication->getUser() === $this) {
            $emailIndication->setUser(null);
        }

        return $this;
    }

       /**
     * @return Collection<int, Pseudo>
     */
    public function getPseudos(): Collection
    {
        return $this->pseudos;
    }

    public function addPseudo(Pseudo $pseudo): self
    {
        if (!$this->pseudos->contains($pseudo)) {
            $this->pseudos[] = $pseudo;
            $pseudo->setUser($this);
        }

        return $this;
    }

    public function removePseudo(Pseudo $pseudo): self
    {
        if ($this->pseudos->removeElement($pseudo)) {
            // set the owning side to null (unless already changed)
            if ($pseudo->getUser() === $this) {
                $pseudo->setUser(null);
            }
        }

        return $this;
    }

}
