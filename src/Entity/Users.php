<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Column;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;




#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\Index(columns:["Nom","Role"],flags:["fulltext"])]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


     
    #[Assert\NotBlank(message:"Name is required")]
    #[Assert\Length(min : 5, minMessage :" Entrer un Nom au mini de 5 caracteres")]
    #[ORM\Column(length: 255)]
    private  $Nom;

   
    #[Assert\NotBlank(message:"Prenom is required")]
    #[Assert\Length(min : 5, minMessage :" Entrer un Prenom au mini de 5 caracteres")]
    #[ORM\Column(length: 255)]
    private  $Prenom;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Email is required")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    private ?string $Mail = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Password is required")]
    #[Assert\Length(min : 4, minMessage :" Entrer un Mot de passe au minimum de 4 caracteres")]
    private ?string $Password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Role is required")]
    private ?string $Role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->Mail;
    }

    public function setMail(string $Mail): self
    {
        $this->Mail = $Mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): self
    {
        $this->Password = $Password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->Role;
    }

    public function setRole(string $Role): self
    {
        $this->Role = $Role;

        return $this;
    }
    public function __toString()
    {
        return $this->Nom; // Assuming `name` is a property that holds a string value
    }
}
