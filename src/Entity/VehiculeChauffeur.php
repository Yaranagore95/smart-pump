<?php

namespace App\Entity;

use App\Repository\VehiculeChauffeurRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VehiculeChauffeurRepository::class)
 */
class VehiculeChauffeur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $immatriculation;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity=Chauffeur::class, inversedBy="vehiculeChauffeurs", cascade={"PERSIST"})
     */
    private ?Chauffeur $chauffeur;

    /**
     * @ORM\OneToMany(targetEntity=BonChauffeur::class, mappedBy="vehiculeChauffeur", cascade={"PERSIST", "REMOVE"})
     */
    private ?Collection $bonChauffeurs;

    /**
     * @ORM\ManyToOne(targetEntity=TypeCarburant::class, inversedBy="vehiculeChauffeurs")
     */
    private ?TypeCarburant $typeCarburant;

    public function __construct()
    {
        $this->bonChauffeurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getChauffeur(): ?Chauffeur
    {
        return $this->chauffeur;
    }

    public function setChauffeur(?Chauffeur $chauffeur): self
    {
        $this->chauffeur = $chauffeur;

        return $this;
    }

    /**
     * @return Collection|BonChauffeur[]
     */
    public function getBonChauffeurs(): Collection
    {
        return $this->bonChauffeurs;
    }

    public function addBonChauffeur(BonChauffeur $bonChauffeur): self
    {
        if (!$this->bonChauffeurs->contains($bonChauffeur)) {
            $this->bonChauffeurs[] = $bonChauffeur;
            $bonChauffeur->setVehiculeChauffeur($this);
        }

        return $this;
    }

    public function removeBonChauffeur(BonChauffeur $bonChauffeur): self
    {
        if ($this->bonChauffeurs->removeElement($bonChauffeur)) {
            // set the owning side to null (unless already changed)
            if ($bonChauffeur->getVehiculeChauffeur() === $this) {
                $bonChauffeur->setVehiculeChauffeur(null);
            }
        }

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
}
