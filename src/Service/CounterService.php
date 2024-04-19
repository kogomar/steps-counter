<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Counter;
use App\Entity\Team;
use App\Params\CreateCounterParams;
use App\Repository\CounterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CounterService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CounterRepository $counterRepository,
    ) {
    }

    public function createCounter(int $teamId, CreateCounterParams $params): Counter
    {
        $team = $this->entityManager->getRepository(Team::class)->find($teamId);
        if (!$team) {
            throw new NotFoundHttpException("Team with id {$teamId} not found.");
        }

        $counter = new Counter();
        $counter->setTeam($team);
        $counter->setStepCount($params->stepCount);
        $counter->setName($params->name);

        $this->entityManager->persist($counter);
        $this->entityManager->flush();

        return $counter;
    }

    public function incrementCounter(int $counterId, int $steps): Counter
    {
        $counter = $this->entityManager->getRepository(Counter::class)->find($counterId);
        if (!$counter) {
            throw new NotFoundHttpException("Counter with id {$counterId} not found.");
        }

        $counter->setStepCount($counter->getStepCount() + $steps);

        $this->entityManager->flush();

        return $counter;
    }

    public function getCountersByTeam(int $teamId): array
    {
        return $this->counterRepository->findCountersByTeam($teamId);
    }

    public function deleteCounter(int $counterId): void
    {
        $counter = $this->entityManager->getRepository(Counter::class)->find($counterId);
        if (!$counter) {
            throw new NotFoundHttpException("Counter not found.");
        }

        $this->entityManager->remove($counter);
        $this->entityManager->flush();
    }
}
