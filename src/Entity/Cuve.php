<?php

namespace App\Entity;

use App\Repository\CuveRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CuveRepository::class)
 */
class Cuve
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
     * @ORM\Column(type="integer")
     */
    private ?int $capacity;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stock;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="cuves")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Station $station;

    /**
     * @ORM\ManyToOne(targetEntity=TypeCarburant::class, inversedBy="cuves")
     * @ORM\JoinColumn(nullable=false)
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
     * @ORM\Column(type="boolean")
     */
    private ?bool $isActived;

    /**
     * @ORM\OneToMany(targetEntity=CuveMesure::class, mappedBy="cuve")
     */
    private ?Collection $cuveMesures;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $prixAchatMoyen;

    /**
     * @ORM\OneToMany(targetEntity=Stockage::class, mappedBy="cuve")
     */
    private ?Collection $stockages;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $qteAlerte;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $lastPrixAchatMoyen;

    /**
     * @ORM\OneToMany(targetEntity=Jaugeage::class, mappedBy="cuve")
     */
    private ?Collection $jaugeages;

    /**
     * @ORM\OneToMany(targetEntity=VenteCuve::class, mappedBy="cuve")
     */
    private ?Collection $venteCuves;

    public function __construct()
    {
        $this->cuveMesures = new ArrayCollection();
        $this->stockages = new ArrayCollection();
        $this->jaugeages = new ArrayCollection();
        $this->venteCuves = new ArrayCollection();
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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

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

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): self
    {
        $this->station = $station;

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

    public function getIsActived(): ?bool
    {
        return $this->isActived;
    }

    public function setIsActived(bool $isActived): self
    {
        $this->isActived = $isActived;

        return $this;
    }

    /**
     * @return Collection|CuveMesure[]
     */
    public function getCuveMesures(): Collection
    {
        return $this->cuveMesures;
    }

    public function addCuveMesure(CuveMesure $cuveMesure): self
    {
        if (!$this->cuveMesures->contains($cuveMesure)) {
            $this->cuveMesures[] = $cuveMesure;
            $cuveMesure->setCuve($this);
        }

        return $this;
    }

    public function removeCuveMesure(CuveMesure $cuveMesure): self
    {
        if ($this->cuveMesures->removeElement($cuveMesure)) {
            // set the owning side to null (unless already changed)
            if ($cuveMesure->getCuve() === $this) {
                $cuveMesure->setCuve(null);
            }
        }

        return $this;
    }

    public function getPrixAchatMoyen(): ?int
    {
        return $this->prixAchatMoyen;
    }

    public function setPrixAchatMoyen(int $prixAchatMoyen): self
    {
        $this->prixAchatMoyen = $prixAchatMoyen;

        return $this;
    }

    /**
     * @return Collection|Stockage[]
     */
    public function getStockages(): Collection
    {
        return $this->stockages;
    }

    public function addStockage(Stockage $stockage): self
    {
        if (!$this->stockages->contains($stockage)) {
            $this->stockages[] = $stockage;
            $stockage->setCuve($this);
        }

        return $this;
    }

    public function removeStockage(Stockage $stockage): self
    {
        if ($this->stockages->removeElement($stockage)) {
            // set the owning side to null (unless already changed)
            if ($stockage->getCuve() === $this) {
                $stockage->setCuve(null);
            }
        }

        return $this;
    }

    public function getQteAlerte(): ?int
    {
        return $this->qteAlerte;
    }

    public function setQteAlerte(int $qteAlerte): self
    {
        $this->qteAlerte = $qteAlerte;

        return $this;
    }

    public function getLastPrixAchatMoyen(): ?int
    {
        return $this->lastPrixAchatMoyen;
    }

    public function setLastPrixAchatMoyen(?int $lastPrixAchatMoyen): self
    {
        $this->lastPrixAchatMoyen = $lastPrixAchatMoyen;

        return $this;
    }

    /**
     * @return Collection|Jaugeage[]
     */
    public function getJaugeages(): Collection
    {
        return $this->jaugeages;
    }

    public function addJaugeage(Jaugeage $jaugeage): self
    {
        if (!$this->jaugeages->contains($jaugeage)) {
            $this->jaugeages[] = $jaugeage;
            $jaugeage->setCuve($this);
        }

        return $this;
    }

    public function removeJaugeage(Jaugeage $jaugeage): self
    {
        if ($this->jaugeages->removeElement($jaugeage)) {
            // set the owning side to null (unless already changed)
            if ($jaugeage->getCuve() === $this) {
                $jaugeage->setCuve(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VenteCuve[]
     */
    public function getVenteCuves(): Collection
    {
        return $this->venteCuves;
    }

    public function addVenteCufe(VenteCuve $venteCufe): self
    {
        if (!$this->venteCuves->contains($venteCufe)) {
            $this->venteCuves[] = $venteCufe;
            $venteCufe->setCuve($this);
        }

        return $this;
    }

    public function removeVenteCufe(VenteCuve $venteCufe): self
    {
        if ($this->venteCuves->removeElement($venteCufe)) {
            // set the owning side to null (unless already changed)
            if ($venteCufe->getCuve() === $this) {
                $venteCufe->setCuve(null);
            }
        }

        return $this;
    }
}
