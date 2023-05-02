<?php

namespace App\Entity;

use App\Repository\CaptchaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaptchaRepository::class)]
class Captcha
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    private string $lienImageCaptcha;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getLienImageCaptcha(): ?string
    {
        return $this->lienImageCaptcha;
    }

    public function setLienImageCaptcha(string $lienImageCaptcha): self
    {
        $this->lienImageCaptcha = $lienImageCaptcha;

        return $this;
    }
}
