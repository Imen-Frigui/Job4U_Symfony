<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SocieteRepository;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass:SocieteRepository::class)]

class Societe implements \JsonSerializable
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id=null;

    #[ORM\Column(length:20)]
    #[Assert\NotBlank(message:"Enter a valid adresse")]
    private ?string $adresse=null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Email is required")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    private ?string $email=null;

    #[ORM\Column(length:255)]
    #[Assert\Length(
        min: 00000000,
        max: 99999999,
        minMessage: "tel must be at least {{ min }} characters long",
        maxMessage: "tel cannot be longer than {{ max }} characters",
    )]
    #[Assert\NotBlank(message:'Enter a valid phone number between {{ min }} and {{ max }}')]
    private ?string $tel=null;

    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Field of work is required")]
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

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'adresse' => $this->adresse,
            'email' => $this->email,
            'tel' => $this->tel,
            'domaine' => $this->domaine,
            'image' => $this->sosImage
        );
    }

    public function constructor($adresse, $email, $tel, $domaine, $image)
    {
        $this->adresse = $adresse;
        $this->email = $email;
        $this->tel = $tel;
        $this->domaine = $domaine;
        $this->sosImage = $image;
    }
}
