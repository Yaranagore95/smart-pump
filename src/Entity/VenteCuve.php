<?php

namespace App\Entity;

use App\Repository\VenteCuveRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VenteCuveRepository::class)
 */
class VenteCuve
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $quantite;

    /**
     * @ORM\Column(type="bigint")
     */
    private ?string $montantAchat;

    /**
     * @ORM\Column(type="bigint")
     */
    private ?string $montantVente;

    /**
     * @ORM\Column(type="bigint")
     */
    private ?string $gain;

    /**
     * @ORM\ManyToOne(targetEntity=Cuve::class, inversedBy="venteCuves")
     */
    private ?Cuve $cuve;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getMontantAchat(): ?string
    {
        return $this->montantAchat;
    }

    public function setMontantAchat(string $montantAchat): self
    {
        $this->montantAchat = $montantAchat;

        return $this;
    }

    public function getMontantVente(): ?string
    {
        return $this->montantVente;
    }

    public function setMontantVente(string $montantVente): self
    {
        $this->montantVente = $montantVente;

        return $this;
    }

    public function getGain(): ?string
    {
        return $this->gain;
    }

    public function setGain(string $gain): self
    {
        $this->gain = $gain;

        return $this;
    }

    public function getCuve(): ?Cuve
    {
        return $this->cuve;
    }

    public function setCuve(?Cuve $cuve): self
    {
        $this->cuve = $cuve;

        return $this;
    }
}
