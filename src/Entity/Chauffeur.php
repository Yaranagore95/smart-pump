<?php

namespace App\Entity;

use App\Repository\ChauffeurRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ChauffeurRepository::class)
 * @Vich\Uploadable()
 */
class Chauffeur
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
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $telephone;

    /**
     * @ORM\ManyToOne(targetEntity=TypeVehicule::class, inversedBy="chauffeurs", cascade={"PERSIST"})
     */
    private ?TypeVehicule $typeVehicule;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $photoPermis;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $updatedAt;

    /**
     * @Assert\Image(mimeTypes={"image/jpg", "image/png", "image/jpeg"}, mimeTypesMessage="Choisir un autre format image")
     * @Vich\UploadableField(mapping="permis_chauffeur", fileNameProperty="photoPermis")
     * @var File|null
     * @Ignore()
     */
    private ?File $imageFile = null;

    /**
     * @ORM\OneToMany(targetEntity=VehiculeChauffeur::class, mappedBy="chauffeur", cascade={"PERSIST", "REMOVE"})
     */
    private ?Collection $vehiculeChauffeurs;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTimeInterface $dateNaissance;

    public function __construct()
    {
        $this->vehiculeChauffeurs = new ArrayCollection();
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


    public function getTypeVehicule(): ?TypeVehicule
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule(?TypeVehicule $typeVehicule): self
    {
        $this->typeVehicule = $typeVehicule;

        return $this;
    }

    public function getPhotoPermis(): ?string
    {
        return $this->photoPermis;
    }

    public function setPhotoPermis(?string $photoPermis): self
    {
        $this->photoPermis = $photoPermis;

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
            $vehiculeChauffeur->setChauffeur($this);
        }

        return $this;
    }

    public function removeVehiculeChauffeur(VehiculeChauffeur $vehiculeChauffeur): self
    {
        if ($this->vehiculeChauffeurs->removeElement($vehiculeChauffeur)) {
            // set the owning side to null (unless already changed)
            if ($vehiculeChauffeur->getChauffeur() === $this) {
                $vehiculeChauffeur->setChauffeur(null);
            }
        }

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }
}
