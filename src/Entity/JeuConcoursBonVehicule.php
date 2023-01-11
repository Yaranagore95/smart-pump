<?php

namespace App\Entity;

use App\Repository\JeuConcoursBonVehiculeRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JeuConcoursBonVehiculeRepository::class)
 */
class JeuConcoursBonVehicule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $matricule;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $quantite;

    /**
     * @ORM\Column(type="bigint")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    private $qteTotal;

    private $montantTotal;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getQuantite(): ?string
    {
        return $this->quantite;
    }

    public function setQuantite(string $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

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

    /**
     * @return mixed
     */
    public function getQteTotal()
    {
        return $this->qteTotal;
    }

    /**
     * @param mixed $qteTotal
     */
    public function setQteTotal($qteTotal): void
    {
        $this->qteTotal = $qteTotal;
    }

    /**
     * @return mixed
     */
    public function getMontantTotal()
    {
        return $this->montantTotal;
    }

    /**
     * @param mixed $montantTotal
     */
    public function setMontantTotal($montantTotal): void
    {
        $this->montantTotal = $montantTotal;
    }
}
