<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Vich\Uploadable()
 * @UniqueEntity("email", message="Cet émail est déjà utilisé.")
 * @UniqueEntity("telephone", message="Cet numéro de téléphone est déjà utilisé.")
 */
class User implements UserInterface, Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @Assert\Email(message="L'émail doit être un email valide")
     * @Assert\NotBlank(message="L'émail ne peut pas être vide")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $email;


    /**
     * @var array
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @Assert\NotBlank(message="Le nom ne peut pas être vide")
     * @ORM\Column(type="string", length=100)
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $prenom;

    /**
     * @Assert\NotBlank(message="Le téléphone ne peut pas être vide")
     * @ORM\Column(type="string", length=100)
     */
    private ?string $telephone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $adresse;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isEnabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isAdmin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $profile;

    /**
     * @Assert\Image(mimeTypes={"image/jpg", "image/png", "image/jpeg"}, mimeTypesMessage="Choisir un autre format image")
     * @Vich\UploadableField(mapping="user_profile", fileNameProperty="profile")
     * @var File|null
     * @Ignore()
     */
    private ?File $imageFile = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=UserRole::class, inversedBy="users")
     */
    private ?Collection $userRoles;

    /**
     * @ORM\ManyToOne(targetEntity=StructureClient::class, inversedBy="users")
     */
    private ?StructureClient $structureClient;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->userRoles->map(function ($role) {
            return 'ROLE_' . $role->getLibelle();
        })->toArray();

        $roles[] = 'ROLE_USER';

        return $roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): self
    {
        $this->profile = $profile;

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
     * @return Collection|UserRole[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(UserRole $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
        }

        return $this;
    }

    public function removeUserRole(UserRole $userRole): self
    {
        $this->userRoles->removeElement($userRole);

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

    /**
     * @return string
     */
    public function serialize(): string
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->roles,
            $this->isEnabled,
            $this->isAdmin
        ));
    }

    /**
     * @param string $data
     */
    public function unserialize($data): void
    {
        [
            $this->id,
            $this->email,
            $this->password,
            $this->roles,
            $this->isEnabled,
            $this->isAdmin
        ] = unserialize($data, ['allowed_classes' => false]);
    }
}
