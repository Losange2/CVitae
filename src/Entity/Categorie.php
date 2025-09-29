<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(targetEntity: Point::class, mappedBy: 'la_cate')]
    private Collection $les_points_cate;

    public function __construct()
    {
        $this->les_points_cate = new ArrayCollection();
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
     * @return Collection<int, Point>
     */
    public function getLesPointsCate(): Collection
    {
        return $this->les_points_cate;
    }

    public function addLesPointsCate(Point $lesPointsCate): static
    {
        if (!$this->les_points_cate->contains($lesPointsCate)) {
            $this->les_points_cate->add($lesPointsCate);
            $lesPointsCate->setLaCate($this);
        }

        return $this;
    }

    public function removeLesPointsCate(Point $lesPointsCate): static
    {
        if ($this->les_points_cate->removeElement($lesPointsCate)) {
            // set the owning side to null (unless already changed)
            if ($lesPointsCate->getLaCate() === $this) {
                $lesPointsCate->setLaCate(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->libelle;
    }
}
