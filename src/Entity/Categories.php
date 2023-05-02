<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Nom field est vide")]
    #[Assert\Length(
        min: 5,
        minMessage: "Description doit etre plus que 5 characters long",
    )]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Offre::class)]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Offre>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Offre $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setCategorie($this);
        }

        return $this;
    }

    public function removeCategory(Offre $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCategorie() === $this) {
                $category->setCategorie(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {

        return $this->getNom();
    }
}
