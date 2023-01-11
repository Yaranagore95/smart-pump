<?php

namespace App\Entity;

use App\Repository\VentePistoletCarburantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VentePistoletCarburantRepository::class)
 */
class VentePistoletCarburant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TypeCarburant::class, inversedBy="ventePistoletCarburants")
     */
    private $typeCarburant;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @ORM\Column(type="integer")
     */
    private $prixVente;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=VentePistoletCarburantDetail::class, mappedBy="ventePistoletCarburant")
     */
    private $ventePistoletCarburantDetails;

    /**
     * @ORM\ManyToOne(targetEntity=Pistolet::class, inversedBy="ventePistoletCarburants")
     */
    private $pistolet;

    public function __construct()
    {
        $this->ventePistoletCarburantDetails = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|VentePistoletCarburantDetail[]
     */
    public function getVentePistoletCarburantDetails(): Collection
    {
        return $this->ventePistoletCarburantDetails;
    }

    public function addVentePistoletCarburantDetail(VentePistoletCarburantDetail $ventePistoletCarburantDetail): self
    {
        if (!$this->ventePistoletCarburantDetails->contains($ventePistoletCarburantDetail)) {
            $this->ventePistoletCarburantDetails[] = $ventePistoletCarburantDetail;
            $ventePistoletCarburantDetail->setVentePistoletCarburant($this);
        }

        return $this;
    }

    public function removeVentePistoletCarburantDetail(VentePistoletCarburantDetail $ventePistoletCarburantDetail): self
    {
        if ($this->ventePistoletCarburantDetails->removeElement($ventePistoletCarburantDetail)) {
            // set the owning side to null (unless already changed)
            if ($ventePistoletCarburantDetail->getVentePistoletCarburant() === $this) {
                $ventePistoletCarburantDetail->setVentePistoletCarburant(null);
            }
        }

        return $this;
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
}
