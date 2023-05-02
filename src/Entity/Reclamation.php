<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;



#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        minMessage: "Votre message doit avoir au minimum 5 caractéres",
        maxMessage: "Votre message doit avoir au maximum 50 caractéres",
    )]
    #[Assert\Regex(
        pattern: "/^[A-Z]/",
        match: true,
        message: "Votre message doit commencer par une lettre majuscule",
    )]
    #[Assert\Regex(
        pattern: "/[#?!@$%^&*-]+/i",
        match: false,
        message: "Votre message ne doit pas contenir un caractére spéciale",
    )]
    private ?string $message;

    #[ORM\Column(length: 255)]
    private ?string $type;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
