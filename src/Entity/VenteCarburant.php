<?php

namespace App\Entity;

use App\Repository\VenteCarburantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VenteCarburantRepository::class)
 */
class VenteCarburant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TypeCarburant::class, inversedBy="venteCarburants")
     */
    private $typeCarburant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $prixAchat;

    /**
     * @ORM\Column(type="integer")
     */
    private $prixVente;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\OneToMany(targetEntity=VenteCarburantDetail::class, mappedBy="venteCarburant", cascade={"persist"})
     */
    private $venteCarburantDetails;

    public function __construct()
    {
        $this->venteCarburantDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeCarburant(): ?TypeCarburant
    {
        return $this->typeCarburant;
    }

    public function setTypeCarburant(?TypeCarburant $typeCarburant): self
    {
        $this->typeCarburant = $typeCarburant;

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

    public function getPrixAchat(): ?int
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(int $prixAchat): self
    {
        $this->prixAchat = $prixAchat;

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

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * @return Collection|VenteCarburantDetail[]
     */
    public function getVenteCarburantDetails(): Collection
    {
        return $this->venteCarburantDetails;
    }

    public function addVenteCarburantDetail(VenteCarburantDetail $venteCarburantDetail): self
    {
        if (!$this->venteCarburantDetails->contains($venteCarburantDetail)) {
            $this->venteCarburantDetails[] = $venteCarburantDetail;
            $venteCarburantDetail->setVenteCarburant($this);
        }

        return $this;
    }

    public function removeVenteCarburantDetail(VenteCarburantDetail $venteCarburantDetail): self
    {
        if ($this->venteCarburantDetails->removeElement($venteCarburantDetail)) {
            // set the owning side to null (unless already changed)
            if ($venteCarburantDetail->getVenteCarburant() === $this) {
                $venteCarburantDetail->setVenteCarburant(null);
            }
        }

        return $this;
    }
}
