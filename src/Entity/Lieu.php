<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(targetEntity: Point::class, mappedBy: 'un_lieu')]
    private Collection $form_emploi;

    #[ORM\ManyToOne(inversedBy: 'appellation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeDeLieu $le_type_l = null;

    public function __construct()
    {
        $this->form_emploi = new ArrayCollection();
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

    /**
     * @return Collection<int, Point>
     */
    public function getFormEmploi(): Collection
    {
        return $this->form_emploi;
    }

    public function addFormEmploi(Point $formEmploi): static
    {
        if (!$this->form_emploi->contains($formEmploi)) {
            $this->form_emploi->add($formEmploi);
            $formEmploi->setUnLieu($this);
        }

        return $this;
    }

    public function removeFormEmploi(Point $formEmploi): static
    {
        if ($this->form_emploi->removeElement($formEmploi)) {
            // set the owning side to null (unless already changed)
            if ($formEmploi->getUnLieu() === $this) {
                $formEmploi->setUnLieu(null);
            }
        }

        return $this;
    }

    public function getLeTypeL(): ?TypeDeLieu
    {
        return $this->le_type_l;
    }

    public function setLeTypeL(?TypeDeLieu $le_type_l): static
    {
        $this->le_type_l = $le_type_l;

        return $this;
    }
    public function __toString()
    {
        return $this->nom;
    }
}
