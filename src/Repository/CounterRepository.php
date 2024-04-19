<?php

namespace App\Repository;

use App\Entity\Counter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CounterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Counter::class);
    }

    public function findCountersByTeam(int $teamId): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('new App\OutputModel\UnifyOutputModel(c.id, c.name, c.stepCount)')
            ->where('c.team = :teamId')
            ->setParameter('teamId', $teamId);

        return $qb->getQuery()->getResult();
    }
}