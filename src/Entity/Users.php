<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Form\SearchType;
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 33)]
    private ?string $Nom = null;

    #[ORM\Column(length: 33)]
    private ?string $Prenom = null;

    #[ORM\Column(length: 40)]
    private ?string $Mail = null;

    #[ORM\Column(length: 33)]
    private ?string $Password = null;

    #[ORM\Column(length: 12)]
    private ?string $Role = null;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Postulation::class)]

    private Collection $creator;

    public function __construct()
    {
        $this->creator = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Postulation>
     */
    public function getCreator(): Collection
    {
        return $this->creator;
    }

    public function addCreator(Postulation $creator): self
    {
        if (!$this->creator->contains($creator)) {
            $this->creator->add($creator);
            $creator->setCreator($this);
        }

        return $this;
    }

    public function removeCreator(Postulation $creator): self
    {
        if ($this->creator->removeElement($creator)) {
            // set the owning side to null (unless already changed)
            if ($creator->getCreator() === $this) {
                $creator->setCreator(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return $this->getNom();
    }
}
