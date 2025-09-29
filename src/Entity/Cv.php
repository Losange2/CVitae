<?php

namespace App\Entity;

use App\Repository\CvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CvRepository::class)]
class Cv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Titre = null;

    #[ORM\ManyToOne(inversedBy: 'les_cv')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $le_client = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(targetEntity: Point::class, mappedBy: 'le_cv')]
    private Collection $les_points;

    public function __construct()
    {
        $this->les_points = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): static
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getLeClient(): ?User
    {
        return $this->le_client;
    }

    public function setLeClient(?User $le_client): static
    {
        $this->le_client = $le_client;

        return $this;
    }

    /**
     * @return Collection<int, Point>
     */
    public function getLesPoints(): Collection
    {
        return $this->les_points;
    }

    public function addLesPoint(Point $lesPoint): static
    {
        if (!$this->les_points->contains($lesPoint)) {
            $this->les_points->add($lesPoint);
            $lesPoint->setLeCv($this);
        }

        return $this;
    }

    public function removeLesPoint(Point $lesPoint): static
    {
        if ($this->les_points->removeElement($lesPoint)) {
            // set the owning side to null (unless already changed)
            if ($lesPoint->getLeCv() === $this) {
                $lesPoint->setLeCv(null);
            }
        }

        return $this;
    }
}
