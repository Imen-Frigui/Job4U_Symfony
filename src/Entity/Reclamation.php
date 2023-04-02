<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reclamation
 *
 * @ORM\Table(name="reclamation", indexes={@ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity
 */
class Reclamation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_reclamation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReclamation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message", type="string", length=255, options={"default"="NULL"})
     * @Assert\NotBlank
     */
    private $message = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=50,  options={"default"="NULL"})
     * @Assert\NotBlank
     */
    private $type = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="statut", type="string", length=50, options={"default"="NULL"})
     * @Assert\NotBlank
     */
    private $statut = '';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="Id")
     * })
     * @Assert\NotBlank
     */
    private $idUser;

    public function getIdReclamation(): ?int
    {
        return $this->idReclamation;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

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


}
