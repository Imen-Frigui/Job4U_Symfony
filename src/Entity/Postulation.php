<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostulationRepository;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass:PostulationRepository::class)]

class Postulation
{ 
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idPos=null;
 



    #[ORM\Column(length:255)]
    #[Assert\NotBlank(message:"Simple User is required")]
    private ?string $simpleUser=null;

   
    #[ORM\Column(length:50)]
    #[Assert\NotBlank(message:"Email is required")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    private ?string $email=null;


    
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "Date of the event not assigned")]
    private ?\DateTimeInterface $date_pos = null;

    
    public function __construct()
    {
        $this->societe = new ArrayCollection();
    }

    public function getIdPos(): ?int
    {
        return $this->idPos;
    }
    public function setIdPos(): ?int
    {
        return $this->idPos;
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
    public function getDatePos(): ?\DateTimeInterface
    {
        return $this->date_pos;
    }

    public function setDatePos(\DateTimeInterface $date_pos): self
    {
        $this->date_pos = $date_pos;

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
