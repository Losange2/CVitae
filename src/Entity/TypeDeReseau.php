<?php

namespace App\Entity;

use App\Repository\TypeDeReseauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDeReseauRepository::class)]
class TypeDeReseau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    /**
     * @var Collection<int, Reseau>
     */
    #[ORM\OneToMany(targetEntity: Reseau::class, mappedBy: 'le_type_r')]
    private Collection $lien;

    public function __construct()
    {
        $this->lien = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Reseau>
     */
    public function getLien(): Collection
    {
        return $this->lien;
    }

    public function addLien(Reseau $lien): static
    {
        if (!$this->lien->contains($lien)) {
            $this->lien->add($lien);
            $lien->setLeTypeR($this);
        }

        return $this;
    }

    public function removeLien(Reseau $lien): static
    {
        if ($this->lien->removeElement($lien)) {
            // set the owning side to null (unless already changed)
            if ($lien->getLeTypeR() === $this) {
                $lien->setLeTypeR(null);
            }
        }

        return $this;
    }
}
