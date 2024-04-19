<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Team;
use App\OutputModel\TeamStepsOutputModel;
use App\OutputModel\UnifyOutputModel;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamRepository $teamRepository,
    ) {
    }

    public function createTeam(string $name): Team
    {
        $team = new Team();
        $team->setName($name);

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }

    public function getTotalStepsByTeam(int $teamId): int
    {
        $team = $this->teamRepository->find($teamId);

        if (!$team) {
            throw new NotFoundHttpException("Team with id {$teamId} not found.");
        }

        $totalSteps = 0;
        foreach ($team->getCounters() as $counter) {
            $totalSteps += $counter->getStepCount();
        }

        return $totalSteps;
    }

    public function getAllTeamsWithStepCounts(): array
    {
        return array_map(function ($result) {
            return new UnifyOutputModel($result['id'], $result['name'], (int) $result['totalSteps']);
        }, $this->teamRepository->findAllTeamsWithStepCounts());
    }

    public function deleteTeam(int $teamId): void
    {
        $team = $this->entityManager->getRepository(Team::class)->find($teamId);
        if (!$team) {
            throw new NotFoundHttpException("Team not found.");
        }

        $this->entityManager->remove($team);
        $this->entityManager->flush();
    }
}
