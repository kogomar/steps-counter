<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CounterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'counter')]
#[ORM\Entity(repositoryClass: CounterRepository::class)]
class Counter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $stepCount = 0;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'counters')]
    #[ORM\JoinColumn(nullable: false)]
    private Team $team;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStepCount(): int
    {
        return $this->stepCount;
    }

    public function setStepCount(int $stepCount): self
    {
        $this->stepCount = $stepCount;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;

        return $this;
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
}