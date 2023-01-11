<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepenseRepository::class)
 */
class Depense
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $libelle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $montant;

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="depenses")
     */
    private ?Station $station;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=DetailDepense::class, mappedBy="depense")
     */
    private ?Collection $detailDepenses;

    public function __construct()
    {
        $this->detailDepenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): self
    {
        $this->station = $station;

        return $this;
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

    /**
     * @return Collection|DetailDepense[]
     */
    public function getDetailDepenses(): Collection
    {
        return $this->detailDepenses;
    }

    public function addDetailDepense(DetailDepense $detailDepense): self
    {
        if (!$this->detailDepenses->contains($detailDepense)) {
            $this->detailDepenses[] = $detailDepense;
            $detailDepense->setDepense($this);
        }

        return $this;
    }

    public function removeDetailDepense(DetailDepense $detailDepense): self
    {
        if ($this->detailDepenses->removeElement($detailDepense)) {
            // set the owning side to null (unless already changed)
            if ($detailDepense->getDepense() === $this) {
                $detailDepense->setDepense(null);
            }
        }

        return $this;
    }
}
