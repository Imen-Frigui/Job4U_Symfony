<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\PosteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass : PosteRepository::class)]
class Poste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    
    private ?int $id=null;
    
    #[Assert\NotBlank(message : "veuillez entrez Un titre valides")]
    #[ORM\Column(length : 200)]
    private ?string $titre=null;
    #[Assert\NotBlank(message : "veuillez entrez une description valides")]
    #[ORM\Column(length : 500)]
    private ?string $description=null;

    #[ORM\Column(length : 250)]
    private ?string $img=null;
    #[Assert\NotBlank(message : "veuillez choisir un domaine")]
    #[ORM\Column(length : 250)]
    private ?string $domaine=null;

    #[ORM\Column(length: '250')]
    private ?string $date=null;

    #[ORM\ManyToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $idUser=null;
    
    #[ORM\OneToMany(targetEntity:Likez::class, mappedBy : "idPoste", cascade :["persist", "remove"])]
   
    private  $likes=null;
    #[ORM\OneToMany(targetEntity:Report::class, mappedBy : "idPoste", cascade :["persist", "remove"])]
   
    private  $reports=null;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }
/**
     * @return Collection|Likez[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }
    /**
    * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addLike(Likez $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Likez $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    public function getTotalLikes(): int
    {
        return count($this->likes);
    }

    public function isLikeByUser(User $Client):bool{
        foreach ($this->likes as $Like){
            if ($Like->getIdUser() === $Client)return true;
        }
        return false;
    }
}
