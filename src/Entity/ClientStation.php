<?php

namespace App\Entity;

use App\Repository\ClientStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientStationRepository::class)
 */
class ClientStation
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
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $prenom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $telephone;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private array $prixCarburant = [];

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="clientStations")
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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $adresse;

    /**
     * @ORM\OneToMany(targetEntity=BonClient::class, mappedBy="clientStation")
     */
    private ?Collection $bonClients;

    public function __construct()
    {
        $this->bonClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getPrixCarburant(): ?array
    {
        return $this->prixCarburant;
    }

    public function setPrixCarburant(?array $prixCarburant): self
    {
        $this->prixCarburant = $prixCarburant;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

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
            $bonClient->setClientStation($this);
        }

        return $this;
    }

    public function removeBonClient(BonClient $bonClient): self
    {
        if ($this->bonClients->removeElement($bonClient)) {
            // set the owning side to null (unless already changed)
            if ($bonClient->getClientStation() === $this) {
                $bonClient->setClientStation(null);
            }
        }

        return $this;
    }
}
