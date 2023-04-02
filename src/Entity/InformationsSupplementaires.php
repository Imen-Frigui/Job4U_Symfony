<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InformationsSupplementaires
 *
 * @ORM\Table(name="informations_supplementaires")
 * @ORM\Entity
 */
class InformationsSupplementaires
{
    /**
     * @var int
     *
     * @ORM\Column(name="Id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=40, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Prenom", type="string", length=40, nullable=false)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="Tell", type="string", length=8, nullable=false)
     */
    private $tell;

    /**
     * @var string
     *
     * @ORM\Column(name="Image", type="string", length=100, nullable=false)
     */
    private $image;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTell(): ?string
    {
        return $this->tell;
    }

    public function setTell(string $tell): self
    {
        $this->tell = $tell;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }


}
