<?php

namespace App\Entity;

use App\Repository\TypeDeLieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDeLieuRepository::class)]
class TypeDeLieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Lieu>
     */
    #[ORM\OneToMany(targetEntity: Lieu::class, mappedBy: 'le_type_l')]
    private Collection $appellation;

    public function __construct()
    {
        $this->appellation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getAppellation(): Collection
    {
        return $this->appellation;
    }

    public function addAppellation(Lieu $appellation): static
    {
        if (!$this->appellation->contains($appellation)) {
            $this->appellation->add($appellation);
            $appellation->setLeTypeL($this);
        }

        return $this;
    }

    public function removeAppellation(Lieu $appellation): static
    {
        if ($this->appellation->removeElement($appellation)) {
            // set the owning side to null (unless already changed)
            if ($appellation->getLeTypeL() === $this) {
                $appellation->setLeTypeL(null);
            }
        }

        return $this;
    }
}
