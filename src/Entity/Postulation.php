<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[Assert\NotBlank(message: "Date of the event not assigned")]
    private ?string $date=null;



    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Simple User is required")]
    private ?string $simpleUser=null;

   
    #[ORM\Column(length:50)]
    #[Assert\NotBlank(message:"Email is required")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    private ?string $email=null;

    
    public function __construct()
    {
        $this->societe = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, societe>
     */
    public function getSociete(): Collection
    {
        return $this->societe;
    }

    public function addSociete(societe $societe): self
    {
        if (!$this->societe->contains($societe)) {
            $this->societe->add($societe);
            $societe->setPostulation($this);
        }

        return $this;
    }

    public function removeSociete(societe $societe): self
    {
        if ($this->societe->removeElement($societe)) {
            // set the owning side to null (unless already changed)
            if ($societe->getPostulation() === $this) {
                $societe->setPostulation(null);
            }
        }

        return $this;
    }


}