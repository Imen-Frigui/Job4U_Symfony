<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message:"Email is required")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    #[ORM\Column(length: 180,type:"string", unique: true)]
    private string $email;

    #[ORM\Column]
    private array $roles = [];


      
    #[Assert\NotBlank(message:"Nom Obligatoire")]
    #[Assert\Length(min : 5, minMessage :" Entrer un Nom au mini de 5 caracteres")]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Votre Nom ne peux pas comporter un nombre',
    )]
    #[ORM\Column(length: 180,type:"string")]
    private $name;
    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank(message:"Password Obligatoire")]
    #[Assert\Length(min : 4, minMessage :" Entrer un Mot de passe au minimum de 4 caracteres")]
   
    #[ORM\Column]
    private ?string $password = null;


     
    #[ORM\Column(length: 180,type:"string")]
    private $reset_token="";


    #[ORM\Column(length: 180,type:"string")]
    private $image="";



    #[ORM\OneToMany(mappedBy: 'creator_id', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Participant::class)]
    private Collection $participants;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
    private Collection $notifications;
    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Postulation::class)]

    private Collection $creator;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reclamation::class)]
    private Collection $reclamations;
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->creator = new ArrayCollection();
        $this->reclamations = new ArrayCollection();


    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }



    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

     /**
     * @return mixed
     */
    public function getResetToken()
    {
        return $this->reset_token;
    }


    /**
     * @param mixed $reset_token
     */
    public function setResetToken($reset_token): void
    {
        $this->reset_token = $reset_token;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCreator($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCreator() === $this) {
                $event->setCreator(null);
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
            $participant->setUser($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getUser() === $this) {
                $participant->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getMessage(): Collection
    {
        return $this->message;
    }

    public function addMessage(Notification $message): self
    {
        if (!$this->message->contains($message)) {
            $this->message->add($message);
            $message->setRelation($this);
        }

        return $this;
    }

    public function removeMessage(Notification $message): self
    {
        if ($this->message->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getRelation() === $this) {
                $message->setRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Postulation>
     */
    public function getCreator(): Collection
    {
        return $this->creator;
    }

    public function addCreator(Postulation $creator): self
    {
        if (!$this->creator->contains($creator)) {
            $this->creator->add($creator);
            $creator->setCreator($this);
        }

        return $this;
    }

    public function removeCreator(Postulation $creator): self
    {
        if ($this->creator->removeElement($creator)) {
            // set the owning side to null (unless already changed)
            if ($creator->getCreator() === $this) {
                $creator->setCreator(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->name; // Assuming `name` is a property that holds a string value
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }

        return $this;
    }
}
