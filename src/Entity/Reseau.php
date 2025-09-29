<?php

namespace App\Entity;

use App\Repository\ReseauRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReseauRepository::class)]
class Reseau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lien = null;

    #[ORM\ManyToOne(inversedBy: 'reseaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $proprio = null;

    #[ORM\ManyToOne(inversedBy: 'lien')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeDeReseau $le_type_r = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getProprio(): ?User
    {
        return $this->proprio;
    }

    public function setProprio(?User $proprio): static
    {
        $this->proprio = $proprio;

        return $this;
    }

    public function getLeTypeR(): ?TypeDeReseau
    {
        return $this->le_type_r;
    }

    public function setLeTypeR(?TypeDeReseau $le_type_r): static
    {
        $this->le_type_r = $le_type_r;

        return $this;
    }
}
