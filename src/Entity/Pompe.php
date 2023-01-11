<?php

namespace App\Entity;

use App\Repository\PompeRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PompeRepository::class)
 */
class Pompe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Station::class, inversedBy="pompes")
     */
    private ?Station $station;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $numero;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Pistolet::class, mappedBy="pompe")
     */
    private ?Collection $pistolets;

    public function __construct()
    {
        $this->pistolets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

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
            $pistolet->setPompe($this);
        }

        return $this;
    }

    public function removePistolet(Pistolet $pistolet): self
    {
        if ($this->pistolets->removeElement($pistolet)) {
            // set the owning side to null (unless already changed)
            if ($pistolet->getPompe() === $this) {
                $pistolet->setPompe(null);
            }
        }

        return $this;
    }
}
