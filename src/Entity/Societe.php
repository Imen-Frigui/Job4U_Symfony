<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SocieteRepository;
#[ORM\Entity(repositoryClass:SocieteRepository::class)]

class Societe
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id=null;

    #[ORM\Column(length:20)]
    private ?string $adresse=null;

    #[ORM\Column(length:255)]
    private ?string $email=null;

    #[ORM\Column(length:255)]
    private ?string $tel=null;

    #[ORM\Column(length:255)]
    private ?string $domaine=null;

    #[ORM\Column(length:255)]
    private ?string $sosImage=null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(string $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    }

    public function getSosImage(): ?string
    {
        return $this->sosImage;
    }

    public function setSosImage(string $sosImage): self
    {
        $this->sosImage = $sosImage;

        return $this;
    }

    public function getPostulation(): ?Postulation
    {
        return $this->postulation;
    }

    public function setPostulation(?Postulation $postulation): self
    {
        $this->postulation = $postulation;

        return $this;
    }


}
