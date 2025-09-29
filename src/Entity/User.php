<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_de_naissance = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    /**
     * @var Collection<int, Cv>
     */
    #[ORM\OneToMany(targetEntity: Cv::class, mappedBy: 'le_client')]
    private Collection $les_cv;

    /**
     * @var Collection<int, Reseau>
     */
    #[ORM\OneToMany(targetEntity: Reseau::class, mappedBy: 'proprio')]
    private Collection $reseaux;

    public function __construct()
    {
        $this->les_cv = new ArrayCollection();
        $this->reseaux = new ArrayCollection();
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
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTime
    {
        return $this->date_de_naissance;
    }

    public function setDateDeNaissance(\DateTime $date_de_naissance): static
    {
        $this->date_de_naissance = $date_de_naissance;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection<int, Cv>
     */
    public function getLesCv(): Collection
    {
        return $this->les_cv;
    }

    public function addLesCv(Cv $lesCv): static
    {
        if (!$this->les_cv->contains($lesCv)) {
            $this->les_cv->add($lesCv);
            $lesCv->setLeClient($this);
        }

        return $this;
    }

    public function removeLesCv(Cv $lesCv): static
    {
        if ($this->les_cv->removeElement($lesCv)) {
            // set the owning side to null (unless already changed)
            if ($lesCv->getLeClient() === $this) {
                $lesCv->setLeClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reseau>
     */
    public function getReseaux(): Collection
    {
        return $this->reseaux;
    }

    public function addReseaux(Reseau $reseaux): static
    {
        if (!$this->reseaux->contains($reseaux)) {
            $this->reseaux->add($reseaux);
            $reseaux->setProprio($this);
        }

        return $this;
    }

    public function removeReseaux(Reseau $reseaux): static
    {
        if ($this->reseaux->removeElement($reseaux)) {
            // set the owning side to null (unless already changed)
            if ($reseaux->getProprio() === $this) {
                $reseaux->setProprio(null);
            }
        }

        return $this;
    }
}
