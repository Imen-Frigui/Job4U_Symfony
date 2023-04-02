<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="reponse", indexes={@ORM\Index(name="id_reclamation", columns={"id_reclamation"})})
 * @ORM\Entity
 */
class Reponse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_reponse", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReponse;

    /**
     * @var string
     *
     * @ORM\Column(name="message_rep", type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 5,
     *      max = 50,
     *      minMessage = "Your message must be at least {{ limit }} characters long",
     *      maxMessage = "Your message cannot be longer than {{ limit }} characters"
     * )
     */
    private $messageRep;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_rep", type="date")
     * 
     * 
     */
    private $dateRep;

    /**
     * @var \Reclamation
     *
     * @ORM\ManyToOne(targetEntity="Reclamation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_reclamation", referencedColumnName="id_reclamation")
     * })
     * @Assert\NotBlank
     */
    private $idReclamation;

    public function getIdReponse(): ?int
    {
        return $this->idReponse;
    }

    public function getMessageRep(): ?string
    {
        return $this->messageRep;
    }

    public function setMessageRep(string $messageRep): self
    {
        $this->messageRep = $messageRep;

        return $this;
    }

    public function getDateRep(): ?\DateTimeInterface
    {
        return $this->dateRep;
    }

    public function setDateRep(\DateTimeInterface $dateRep): self
    {
        $this->dateRep = $dateRep;

        return $this;
    }

    public function getIdReclamation(): ?Reclamation
    {
        return $this->idReclamation;
    }

    public function setIdReclamation(?Reclamation $idReclamation): self
    {
        $this->idReclamation = $idReclamation;

        return $this;
    }


}
