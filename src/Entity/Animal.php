<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
#[Vich\Uploadable]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $state = null;

    #[ORM\OneToOne(inversedBy: 'animal', cascade: ['persist'])]
    private ?RapportVeterinaire $veterinaryReport = null;

    #[ORM\ManyToMany(targetEntity: Race::class, inversedBy: 'animals')]
    private Collection $race;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    #[ORM\Column(type: 'integer')]
    private $views = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastFedAt;

    public function __construct()
    {
        $this->race = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

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

    /**
     * @return Collection<int, Race>
     */
    public function getRace(): Collection
    {
        return $this->race;
    }

    public function addRace(Race $race): static
    {
        if (!$this->race->contains($race)) {
            $this->race->add($race);
        }

        return $this;
    }

    public function removeRace(Race $race): static
    {
        $this->race->removeElement($race);

        return $this;
    }


    public function getRaceLabels(): string
    {
        $labels = [];

        foreach ($this->race as $race) {
            $labels[] = $race->getLabel();
        }

        return implode(', ', $labels);
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }
    
    public function addView(): self
    {
        $this->views++;

        return $this;
    }

    public function getLastFedAt(): ?\DateTimeInterface
    {
        return $this->lastFedAt;
    }

    public function setLastFedAt(?\DateTimeInterface $lastFedAt): self
    {
        $this->lastFedAt = $lastFedAt;

        return $this;
    }
}
