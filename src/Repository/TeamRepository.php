<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function findAllTeamsWithStepCounts(): array
    {
        $qb = $this->createQueryBuilder('team')
            ->select('team.id', 'team.name', 'SUM(counter.stepCount) AS totalSteps')
            ->leftJoin('team.counters', 'counter')
            ->groupBy('team.id');

        return $qb->getQuery()->getResult();
    }
}