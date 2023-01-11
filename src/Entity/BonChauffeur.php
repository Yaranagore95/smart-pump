<?php

namespace App\Entity;

use App\Repository\BonChauffeurRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=BonChauffeurRepository::class)
 * @Vich\Uploadable()
 */
class BonChauffeur
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
    private ?float $quantite;


    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private ?string $montant;

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
    private ?string $pieceJointe;

    /**
     * @Assert\Image(mimeTypes={"image/jpg", "image/png", "image/jpeg"}, mimeTypesMessage="Choisir un autre format image")
     * @Vich\UploadableField(mapping="bon_chauffeur", fileNameProperty="pieceJointe")
     * @var File|null
     * @Ignore()
     */
    private ?File $imageFile = null;

    /**
     * @ORM\ManyToOne(targetEntity=VehiculeChauffeur::class, inversedBy="bonChauffeurs", cascade={"PERSIST"})
     */
    private ?VehiculeChauffeur $vehiculeChauffeur;

    public function getId(): ?float
    {
        return $this->id;
    }


    public function setQuantite(float $quantite): self
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

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPieceJointe(): ?string
    {
        return $this->pieceJointe;
    }

    public function setPieceJointe(?string $pieceJointe): self
    {
        $this->pieceJointe = $pieceJointe;

        return $this;
    }

    public function getVehiculeChauffeur(): ?VehiculeChauffeur
    {
        return $this->vehiculeChauffeur;
    }

    public function setVehiculeChauffeur(?VehiculeChauffeur $vehiculeChauffeur): self
    {
        $this->vehiculeChauffeur = $vehiculeChauffeur;

        return $this;
    }

    /**
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    /**
     * @return File|null
     * @Ignore
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @return float|null
     */
    public function getQuantite(): ?float
    {
        return $this->quantite;
    }
}
