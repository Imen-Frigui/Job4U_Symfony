<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LikezRepository;

#[ORM\Entity(repositoryClass : LikezRepository::class)]
class Likez {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id=null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $idUser=null;

    #[ORM\ManyToOne(targetEntity: Poste::class)]
    #[ORM\JoinColumn(name: 'id_poste', referencedColumnName: 'id')]
    private ?Poste $idPoste=null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdPoste(): ?Poste
    {
        return $this->idPoste;
    }

    public function setIdPoste(?Poste $idPoste): self
    {
        $this->idPoste = $idPoste;

        return $this;
    }

}