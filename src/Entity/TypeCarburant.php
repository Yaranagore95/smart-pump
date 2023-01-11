<?php

namespace App\Entity;

use App\Repository\TypeCarburantRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeCarburantRepository::class)
 */
class TypeCarburant
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
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $prixLittre;

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="typeCarburants")
     */
    private ?Station $station;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Cuve::class, mappedBy="typeCarburant", orphanRemoval=true)
     */
    private ?Collection $cuves;

    /**
     * @ORM\OneToMany(targetEntity=Pistolet::class, mappedBy="typeCarburant")
     */
    private ?Collection $pistolets;

    /**
     * @ORM\OneToMany(targetEntity=BonClient::class, mappedBy="typeCarburant")
     */
    private ?Collection $bonClients;

    /**
     * @ORM\OneToMany(targetEntity=GlobalStockage::class, mappedBy="typeCarburant")
     */
    private ?Collection $globalStockages;

    /**
     * @ORM\OneToMany(targetEntity=RetourEnCuve::class, mappedBy="typeCarburant")
     */
    private ?Collection $retourEnCuves;

    /**
     * @ORM\OneToMany(targetEntity=BonChauffeur::class, mappedBy="typeCarburant")
     */
    private ?Collection $bonChauffeurs;

    /**
     * @ORM\OneToMany(targetEntity=VehiculeChauffeur::class, mappedBy="typeCarburant")
     */
    private ?Collection $vehiculeChauffeurs;

    public function __construct()
    {
        $this->cuves = new ArrayCollection();
        $this->pistolets = new ArrayCollection();
        $this->bonClients = new ArrayCollection();
        $this->globalStockages = new ArrayCollection();
        $this->retourEnCuves = new ArrayCollection();
        $this->vehiculeChauffeurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPrixLittre(): ?int
    {
        return $this->prixLittre;
    }

    public function setPrixLittre(int $prixLittre): self
    {
        $this->prixLittre = $prixLittre;

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
     * @return Collection|Cuve[]
     */
    public function getCuves(): Collection
    {
        return $this->cuves;
    }

    public function addCufe(Cuve $cufe): self
    {
        if (!$this->cuves->contains($cufe)) {
            $this->cuves[] = $cufe;
            $cufe->setTypeCarburant($this);
        }

        return $this;
    }

    public function removeCufe(Cuve $cufe): self
    {
        if ($this->cuves->removeElement($cufe)) {
            // set the owning side to null (unless already changed)
            if ($cufe->getTypeCarburant() === $this) {
                $cufe->setTypeCarburant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Pistolet[]
     */
    public function getPistolets(): Collection
    {
        return $this->pistolets;
    }

    public function addPistolet(Pistolet $pistolet): self
    {
        if (!$this->pistolets->contains($pistolet)) {
            $this->pistolets[] = $pistolet;
            $pistolet->setTypeCarburant($this);
        }

        return $this;
    }

    public function removePistolet(Pistolet $pistolet): self
    {
        if ($this->pistolets->removeElement($pistolet)) {
            // set the owning side to null (unless already changed)
            if ($pistolet->getTypeCarburant() === $this) {
                $pistolet->setTypeCarburant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BonClient[]
     */
    public function getBonClients(): Collection
    {
        return $this->bonClients;
    }

    public function addBonClient(BonClient $bonClient): self
    {
        if (!$this->bonClients->contains($bonClient)) {
            $this->bonClients[] = $bonClient;
            $bonClient->setTypeCarburant($this);
        }

        return $this;
    }

    public function removeBonClient(BonClient $bonClient): self
    {
        if ($this->bonClients->removeElement($bonClient)) {
            // set the owning side to null (unless already changed)
            if ($bonClient->getTypeCarburant() === $this) {
                $bonClient->setTypeCarburant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GlobalStockage[]
     */
    public function getGlobalStockages(): Collection
    {
        return $this->globalStockages;
    }

    public function addGlobalStockage(GlobalStockage $globalStockage): self
    {
        if (!$this->globalStockages->contains($globalStockage)) {
            $this->globalStockages[] = $globalStockage;
            $globalStockage->setTypeCarburant($this);
        }

        return $this;
    }

    public function removeGlobalStockage(GlobalStockage $globalStockage): self
    {
        if ($this->globalStockages->removeElement($globalStockage)) {
            // set the owning side to null (unless already changed)
            if ($globalStockage->getTypeCarburant() === $this) {
                $globalStockage->setTypeCarburant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RetourEnCuve[]
     */
    public function getRetourEnCuves(): Collection
    {
        return $this->retourEnCuves;
    }

    public function addRetourEnCufe(RetourEnCuve $retourEnCufe): self
    {
        if (!$this->retourEnCuves->contains($retourEnCufe)) {
            $this->retourEnCuves[] = $retourEnCufe;
            $retourEnCufe->setTypeCarburant($this);
        }

        return $this;
    }

    public function removeRetourEnCufe(RetourEnCuve $retourEnCufe): self
    {
        if ($this->retourEnCuves->removeElement($retourEnCufe)) {
            // set the owning side to null (unless already changed)
            if ($retourEnCufe->getTypeCarburant() === $this) {
                $retourEnCufe->setTypeCarburant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VehiculeChauffeur[]
     */
    public function getVehiculeChauffeurs(): Collection
    {
        return $this->vehiculeChauffeurs;
    }

    public function addVehiculeChauffeur(VehiculeChauffeur $vehiculeChauffeur): self
    {
        if (!$this->vehiculeChauffeurs->contains($vehiculeChauffeur)) {
            $this->vehiculeChauffeurs[] = $vehiculeChauffeur;
            $vehiculeChauffeur->setTypeCarburant($this);
        }

        return $this;
    }

    public function removeVehiculeChauffeur(VehiculeChauffeur $vehiculeChauffeur): self
    {
        if ($this->vehiculeChauffeurs->removeElement($vehiculeChauffeur)) {
            // set the owning side to null (unless already changed)
            if ($vehiculeChauffeur->getTypeCarburant() === $this) {
                $vehiculeChauffeur->setTypeCarburant(null);
            }
        }

        return $this;
    }
}
