<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    private array $roles;


    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: "users", cascade: ["persist"])]
    private Collection $roleObjects;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?RapportVeterinaire $veterinaryReport = null;


    public function __construct()
    {
        $this->roleObjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        if ($this->roleObjects === null || !($this->roleObjects instanceof Collection)) {
            return ['ROLE_USER'];
        }
    
        $rolesArray = $this->roleObjects->map(function ($role) {
            return $role->getLabel();
        })->toArray();
        
        $rolesArray[] = 'ROLE_USER';
        
        return array_unique($rolesArray);
    }

    public function getRoleObjects(): Collection
    {
        return $this->roleObjects;
    }

    public function setRoleObjects(Collection $roles): self
    {
        $this->roleObjects = $roles;

        // Convert the PersistentCollection to an array
        $this->roles = $roles->toArray();

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roleObjects->contains($role)) {
            $this->roleObjects->add($role);
            $role->addUser($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roleObjects->contains($role)) {
            $this->roleObjects->removeElement($role);
            $role->removeUser($this);
        }

        return $this;
    }

    public function getVeterinaryReport(): ?RapportVeterinaire
    {
        return $this->veterinaryReport;
    }

    public function setVeterinaryReport(?RapportVeterinaire $veterinaryReport): static
    {
        $this->veterinaryReport = $veterinaryReport;

        return $this;
    }
}
