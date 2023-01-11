<?php

namespace App\Entity;

use App\Repository\VenteCarburantDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VenteCarburantDetailRepository::class)
 */
class VenteCarburantDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cuve::class, inversedBy="venteCarburantDetails")
     */
    private $cuve;

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
    private $prixUnitaireVente;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=VenteCarburant::class, inversedBy="venteCarburantDetails", cascade={"persist"})
     */
    private $venteCarburant;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrixUnitaireVente(): ?int
    {
        return $this->prixUnitaireVente;
    }

    public function setPrixUnitaireVente(int $prixUnitaireVente): self
    {
        $this->prixUnitaireVente = $prixUnitaireVente;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getVenteCarburant(): ?VenteCarburant
    {
        return $this->venteCarburant;
    }

    public function setVenteCarburant(?VenteCarburant $venteCarburant): self
    {
        $this->venteCarburant = $venteCarburant;

        return $this;
    }
}
