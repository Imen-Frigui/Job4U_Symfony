<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use App\Entity\EventCategory;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Title field is empty")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Description field is empty")]
    #[Assert\Length(
        min: 7,
        max: 255,
        minMessage: "Description must be at least {{ min }} characters long",
        maxMessage: "Description cannot be longer than {{ max }} characters",
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Date of the event not assigned")]
    #[Assert\GreaterThanOrEqual("today", message: "The date of the event cannot be in the past.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Location field is empty")]
    private ?string $location = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Participant::class)]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[Assert\NotBlank(message: "The event category is not assigned")]
    private ?EventCategory $eventCategory = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Notification::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

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
            $participant->setEvent($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getEvent() === $this) {
                $participant->setEvent(null);
            }
        }

        return $this;
    }

    public function getEventCategory(): ?EventCategory
    {
        return $this->eventCategory;
    }

    public function setEventCategory(?EventCategory $eventCategory): self
    {
        $this->eventCategory = $eventCategory;

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
            $notification->setEvent($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getEvent() === $this) {
                $notification->setEvent(null);
            }
        }

        return $this;
    }
}
