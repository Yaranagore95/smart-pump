<?php

namespace App\Entity;

use App\Repository\StationRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=StationRepository::class)
 * @UniqueEntity("name", message="Cette station existe déjà")
 */
class Station
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
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $adresse;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $telephone;

    /**
     * @ORM\ManyToOne(targetEntity=StructureClient::class, inversedBy="stations")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?StructureClient $structureClient;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Gerant::class, mappedBy="station")
     */
    private ?Collection $gerants;

    /**
     * @ORM\OneToMany(targetEntity=TypeCarburant::class, mappedBy="station")
     */
    private ?Collection $typeCarburants;

    /**
     * @ORM\OneToMany(targetEntity=ClientStation::class, mappedBy="station")
     */
    private ?Collection $clientStations;

    /**
     * @ORM\OneToMany(targetEntity=Cuve::class, mappedBy="station", orphanRemoval=true)
     */
    private ?Collection $cuves;

    /**
     * @ORM\OneToMany(targetEntity=Pompe::class, mappedBy="station")
     */
    private ?Collection $pompes;

    /**
     * @ORM\OneToMany(targetEntity=Depense::class, mappedBy="station")
     */
    private ?Collection $depenses;

    /**
     * @ORM\OneToMany(targetEntity=TypeVehicule::class, mappedBy="station")
     */
    private ?Collection $typeVehicules;

    public function __construct()
    {
        $this->gerants = new ArrayCollection();
        $this->typeCarburants = new ArrayCollection();
        $this->clientStations = new ArrayCollection();
        $this->cuves = new ArrayCollection();
        $this->pompes = new ArrayCollection();
        $this->depenses = new ArrayCollection();
        $this->typeVehicules = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getStructureClient(): ?StructureClient
    {
        return $this->structureClient;
    }

    public function setStructureClient(?StructureClient $structureClient): self
    {
        $this->structureClient = $structureClient;

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
     * @return Collection|Gerant[]
     */
    public function getGerants(): Collection
    {
        return $this->gerants;
    }

    public function addGerant(Gerant $gerant): self
    {
        if (!$this->gerants->contains($gerant)) {
            $this->gerants[] = $gerant;
            $gerant->setStation($this);
        }

        return $this;
    }

    public function removeGerant(Gerant $gerant): self
    {
        if ($this->gerants->removeElement($gerant)) {
            // set the owning side to null (unless already changed)
            if ($gerant->getStation() === $this) {
                $gerant->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TypeCarburant[]
     */
    public function getTypeCarburants(): Collection
    {
        return $this->typeCarburants;
    }

    public function addTypeCarburant(TypeCarburant $typeCarburant): self
    {
        if (!$this->typeCarburants->contains($typeCarburant)) {
            $this->typeCarburants[] = $typeCarburant;
            $typeCarburant->setStation($this);
        }

        return $this;
    }

    public function removeTypeCarburant(TypeCarburant $typeCarburant): self
    {
        if ($this->typeCarburants->removeElement($typeCarburant)) {
            // set the owning side to null (unless already changed)
            if ($typeCarburant->getStation() === $this) {
                $typeCarburant->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ClientStation[]
     */
    public function getClientStations(): Collection
    {
        return $this->clientStations;
    }

    public function addClientStation(ClientStation $clientStation): self
    {
        if (!$this->clientStations->contains($clientStation)) {
            $this->clientStations[] = $clientStation;
            $clientStation->setStation($this);
        }

        return $this;
    }

    public function removeClientStation(ClientStation $clientStation): self
    {
        if ($this->clientStations->removeElement($clientStation)) {
            // set the owning side to null (unless already changed)
            if ($clientStation->getStation() === $this) {
                $clientStation->setStation(null);
            }
        }

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
            $cufe->setStation($this);
        }

        return $this;
    }

    public function removeCufe(Cuve $cufe): self
    {
        if ($this->cuves->removeElement($cufe)) {
            // set the owning side to null (unless already changed)
            if ($cufe->getStation() === $this) {
                $cufe->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Pompe[]
     */
    public function getPompes(): Collection
    {
        return $this->pompes;
    }

    public function addPompe(Pompe $pompe): self
    {
        if (!$this->pompes->contains($pompe)) {
            $this->pompes[] = $pompe;
            $pompe->setStation($this);
        }

        return $this;
    }

    public function removePompe(Pompe $pompe): self
    {
        if ($this->pompes->removeElement($pompe)) {
            // set the owning side to null (unless already changed)
            if ($pompe->getStation() === $this) {
                $pompe->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Depense[]
     */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(Depense $depense): self
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses[] = $depense;
            $depense->setStation($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): self
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getStation() === $this) {
                $depense->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TypeVehicule[]
     */
    public function getTypeVehicules(): Collection
    {
        return $this->typeVehicules;
    }

    public function addTypeVehicule(TypeVehicule $typeVehicule): self
    {
        if (!$this->typeVehicules->contains($typeVehicule)) {
            $this->typeVehicules[] = $typeVehicule;
            $typeVehicule->setStation($this);
        }

        return $this;
    }

    public function removeTypeVehicule(TypeVehicule $typeVehicule): self
    {
        if ($this->typeVehicules->removeElement($typeVehicule)) {
            // set the owning side to null (unless already changed)
            if ($typeVehicule->getStation() === $this) {
                $typeVehicule->setStation(null);
            }
        }

        return $this;
    }
}
