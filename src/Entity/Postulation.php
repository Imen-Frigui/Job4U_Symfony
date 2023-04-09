<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostulationRepository;
#[ORM\Entity(repositoryClass:PostulationRepository::class)]

class Postulation
{ 
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idPos=null;
 
    #[ORM\Column(length:255)]
    #[Assert\Length(min : 5, minMessage :' Entrer un date valid')]
    private ?string $date=null;



    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Simple User is required")]
    private ?string $simpleUser=null;

   
    #[ORM\Column(length:50)]
    #[Assert\NotBlank(message:"Email is required")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    private ?string $email=null;

    public function getIdPos(): ?int
    {
        return $this->idPos;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSimpleUser(): ?string
    {
        return $this->simpleUser;
    }

    public function setSimpleUser(string $simpleUser): self
    {
        $this->simpleUser = $simpleUser;

        return $this;
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


}
