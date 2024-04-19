<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Table(name: 'team')]
#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\OneToMany(targetEntity: Counter::class, mappedBy: 'team', orphanRemoval: true)]
    private Collection $counters;

    public function __construct()
    {
        $this->counters = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCounters(): Collection
    {
        return $this->counters;
    }

    public function addCounter(Counter $counter): self
    {
        if (!$this->counters->contains($counter)) {
            $this->counters[] = $counter;
            $counter->setTeam($this);
        }

        return $this;
    }

    public function removeCounter(Counter $counter): self
    {
        if ($this->counters->contains($counter)) {
            $this->counters->removeElement($counter);
            // set the owning side to null (unless already changed)
            if ($counter->getTeam() === $this) {
                $counter->setTeam(null);
            }
        }

        return $this;
    }
}