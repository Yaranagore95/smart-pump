<?php

namespace App\Entity;

use App\Repository\VentePistoletCarburantDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VentePistoletCarburantDetailRepository::class)
 */
class VentePistoletCarburantDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Pistolet::class, inversedBy="ventePistoletCarburantDetails")
     */
    private $pistolet;

    /**
     * @ORM\ManyToOne(targetEntity=VentePistoletCarburant::class, inversedBy="ventePistoletCarburantDetails")
     */
    private $ventePistoletCarburant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer")
     */
    private $prixVente;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPistolet(): ?Pistolet
    {
        return $this->pistolet;
    }

    public function setPistolet(?Pistolet $pistolet): self
    {
        $this->pistolet = $pistolet;

        return $this;
    }

    public function getVentePistoletCarburant(): ?VentePistoletCarburant
    {
        return $this->ventePistoletCarburant;
    }

    public function setVentePistoletCarburant(?VentePistoletCarburant $ventePistoletCarburant): self
    {
        $this->ventePistoletCarburant = $ventePistoletCarburant;

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

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixVente(): ?int
    {
        return $this->prixVente;
    }

    public function setPrixVente(int $prixVente): self
    {
        $this->prixVente = $prixVente;

        return $this;
    }
}
