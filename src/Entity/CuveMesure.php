<?php

namespace App\Entity;

use App\Repository\CuveMesureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CuveMesureRepository::class)
 */
class CuveMesure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $levelCm;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $volume;

    /**
     * @ORM\ManyToOne(targetEntity=Cuve::class, inversedBy="cuveMesures")
     */
    private ?Cuve $cuve;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevelCm(): ?int
    {
        return $this->levelCm;
    }

    public function setLevelCm(int $levelCm): self
    {
        $this->levelCm = $levelCm;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getCuve(): ?Cuve
    {
        return $this->cuve;
    }

    public function setCuve(?Cuve $cuve): self
    {
        $this->cuve = $cuve;

        return $this;
    }
}
