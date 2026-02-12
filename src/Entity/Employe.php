<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\EmployeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
// Validation Symfony : empêche deux comptes avec le même email
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class Employe implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    // unique: true crée une contrainte UNIQUE en BDD
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateEntree = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    // Tableau des rôles stocké en BDD
    #[ORM\Column]
    private array $roles = [];

    // Contient le mot de passe Hashé
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Projet>
     */
    #[ORM\ManyToMany(targetEntity: Projet::class, mappedBy: 'employes')]
    private Collection $projets;

    /**
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'employe')]
    private Collection $taches;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->taches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
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

    public function getDateEntree(): ?\DateTime
    {
        return $this->dateEntree;
    }

    public function setDateEntree(\DateTime $dateEntree): static
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Projet>
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): static
    {
        if (!$this->projets->contains($projet)) {
            $this->projets->add($projet);
            $projet->addEmploye($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): static
    {
        if ($this->projets->removeElement($projet)) {
            $projet->removeEmploye($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): static
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setEmploye($this);
        }

        return $this;
    }

    public function removeTache(Tache $tache): static
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getEmploye() === $this) {
                $tache->setEmploye(null);
            }
        }

        return $this;
    }

    // Retourne l'identifiant unique de l'utilisateur, ici le mail
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // Retourne les rôles de l'utilisateur
    public function getRoles(): array
    {
        $roles = $this->roles;
        // On garantit toujours un rôle de base
        $roles[] = 'ROLE_USER';

        // On supprime les doublons
        return array_unique($roles);
    }

    // Définit les rôles de l'utilisateur
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Retourne le mot de passe hashé
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Définit le mot de passe
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // On évite de garder en mémoire le mot de passe
    public function eraseCredentials(): void
    {
        // Rien à effacer pour l’instant
    }
}
