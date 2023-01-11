<?php

namespace App\Entity;

use App\Repository\StockageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockageRepository::class)
 */
class Stockage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer")
     */
    private $prixUnitaire;

    /**
     * @ORM\Column(type="integer")
     */
    private $manquant;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLast;

    /**
     * @ORM\ManyToOne(targetEntity=Cuve::class, inversedBy="stockages")
     */
    private $cuve;

    /**
     * @ORM\ManyToOne(targetEntity=GlobalStockage::class, inversedBy="stockages")
     */
    private $gloabalStockage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixUnitaire(): ?int
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(int $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getManquant(): ?int
    {
        return $this->manquant;
    }

    public function setManquant(int $manquant): self
    {
        $this->manquant = $manquant;

        return $this;
    }

    public function getIsLast(): ?bool
    {
        return $this->isLast;
    }

    public function setIsLast(bool $isLast): self
    {
        $this->isLast = $isLast;

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

    public function getGloabalStockage(): ?GlobalStockage
    {
        return $this->gloabalStockage;
    }

    public function setGloabalStockage(?GlobalStockage $gloabalStockage): self
    {
        $this->gloabalStockage = $gloabalStockage;

        return $this;
    }
}
