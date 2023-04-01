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
    private ?string $date=null;


    #[ORM\Column(length:255)]
    private ?string $simpleUser=null;

   
    #[ORM\Column(length:50)]
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
