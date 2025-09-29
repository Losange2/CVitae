<?php

namespace App\Entity;

use App\Repository\PointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointRepository::class)]
class Point
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'les_points')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cv $le_cv = null;

    #[ORM\ManyToOne(inversedBy: 'les_points_cate')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $la_cate = null;

    #[ORM\ManyToOne(inversedBy: 'form_emploi')]
    private ?Lieu $un_lieu = null;

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

    public function getLeCv(): ?Cv
    {
        return $this->le_cv;
    }

    public function setLeCv(?Cv $le_cv): static
    {
        $this->le_cv = $le_cv;

        return $this;
    }

    public function getLaCate(): ?Categorie
    {
        return $this->la_cate;
    }

    public function setLaCate(?Categorie $la_cate): static
    {
        $this->la_cate = $la_cate;

        return $this;
    }

    public function getUnLieu(): ?Lieu
    {
        return $this->un_lieu;
    }

    public function setUnLieu(?Lieu $un_lieu): static
    {
        $this->un_lieu = $un_lieu;

        return $this;
    }
}
