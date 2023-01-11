<?php

namespace App\Entity;

use App\Repository\PistoletRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PistoletRepository::class)
 */
class Pistolet
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
    private ?string $numero;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $indexPistolet;

    /**
     * @ORM\ManyToOne(targetEntity=Pompe::class, inversedBy="pistolets")
     */
    private ?Pompe $pompe;

    /**
     * @ORM\ManyToOne(targetEntity=TypeCarburant::class, inversedBy="pistolets")
     */
    private ?TypeCarburant $typeCarburant;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Indexation::class, mappedBy="pistolet")
     */
    private ?Collection $indexations;

    /**
     * @ORM\OneToMany(targetEntity=VentePistolet::class, mappedBy="pistolet")
     */
    private ?Collection $ventePistolets;

    public function __construct()
    {
        $this->indexations = new ArrayCollection();
        $this->ventePistolets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getIndexPistolet(): ?float
    {
        return $this->indexPistolet;
    }

    public function setIndexPistolet(float $indexPistolet): self
    {
        $this->indexPistolet = $indexPistolet;

        return $this;
    }

    public function getPompe(): ?Pompe
    {
        return $this->pompe;
    }

    public function setPompe(?Pompe $pompe): self
    {
        $this->pompe = $pompe;

        return $this;
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Indexation[]
     */
    public function getIndexations(): Collection
    {
        return $this->indexations;
    }

    public function addIndexation(Indexation $indexation): self
    {
        if (!$this->indexations->contains($indexation)) {
            $this->indexations[] = $indexation;
            $indexation->setPistolet($this);
        }

        return $this;
    }

    public function removeIndexation(Indexation $indexation): self
    {
        if ($this->indexations->removeElement($indexation)) {
            // set the owning side to null (unless already changed)
            if ($indexation->getPistolet() === $this) {
                $indexation->setPistolet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VentePistolet[]
     */
    public function getVentePistolets(): Collection
    {
        return $this->ventePistolets;
    }

    public function addVentePistolet(VentePistolet $ventePistolet): self
    {
        if (!$this->ventePistolets->contains($ventePistolet)) {
            $this->ventePistolets[] = $ventePistolet;
            $ventePistolet->setPistolet($this);
        }

        return $this;
    }

    public function removeVentePistolet(VentePistolet $ventePistolet): self
    {
        if ($this->ventePistolets->removeElement($ventePistolet)) {
            // set the owning side to null (unless already changed)
            if ($ventePistolet->getPistolet() === $this) {
                $ventePistolet->setPistolet(null);
            }
        }

        return $this;
    }
}
