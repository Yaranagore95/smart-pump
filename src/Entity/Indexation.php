<?php

namespace App\Entity;

use App\Repository\IndexationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IndexationRepository::class)
 */
class Indexation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $valIndex;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $difference;

    /**
     * @ORM\ManyToOne(targetEntity=Pistolet::class, inversedBy="indexations")
     */
    private ?Pistolet $pistolet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValIndex(): ?float
    {
        return $this->valIndex;
    }

    public function setValIndex(float $valIndex): self
    {
        $this->valIndex = $valIndex;

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

    public function getDifference(): ?float
    {
        return $this->difference;
    }

    public function setDifference(float $difference): self
    {
        $this->difference = $difference;

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
