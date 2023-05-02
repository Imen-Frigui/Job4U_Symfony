<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;


    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max : 50,
        minMessage : "Votre message doit avoir au minimum 5 caractéres",
        maxMessage : "Votre message doit avoir au maximum 50 caractéres",
    )]
    #[Assert\Regex(
        pattern:"/^[A-Z]/",
        match :true,
        message :"Votre message doit commencer par une lettre majuscule"
    )]
    #[Assert\Regex(
        pattern:"/[#?!@$%^&*-]+/i",
        match :false,
        message :"Votre message ne doit pas contenir un caractére spéciale"
    )]
    private ?string $messageRep;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateRep;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName:"id")]
    private ?Reclamation $idReclamation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageRep(): ?string
    {
        return $this->messageRep;
    }

    public function setMessageRep(string $messageRep): self
    {
        $this->messageRep = $messageRep;

        return $this;
    }

    public function getDateRep(): ?\DateTimeInterface
    {
        return $this->dateRep;
    }

    public function setDateRep(\DateTimeInterface $dateRep): self
    {
        $this->dateRep = $dateRep;

        return $this;
    }

    public function getIdReclamation(): ?Reclamation
    {
        return $this->idReclamation;
    }

    public function setIdReclamation(?Reclamation $idReclamation): self
    {
        $this->idReclamation = $idReclamation;

        return $this;
    }
    public function __toString(): string
    {
        return $this->messageRep;
    }


}
