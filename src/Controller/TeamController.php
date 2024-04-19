<?php

declare(strict_types=1);

namespace App\Controller;

use App\OutputModel\UnifyOutputModel;
use App\Params\CreateTeamParams;
use App\Service\TeamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/teams')]
class TeamController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    #[OA\Get(
        operationId: 'listTeamsWithStepCounts',
        summary: 'List all teams with their total step counts',
        tags: ['Team'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful retrieval of all teams and their step counts',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        description: 'A list of teams with their total step counts',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: UnifyOutputModel::class),
                            type: 'object'
                        )
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'No teams found'
            )
        ]
    )]
    public function list(TeamService $teamService): Response
    {
        return $this->json($teamService->getAllTeamsWithStepCounts());
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        operationId: 'createTeam',
        summary: 'Create a new team',
        requestBody: new OA\RequestBody(
            description: 'JSON payload',
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['name'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Team name')
                    ],
                    type: 'object'
                )
            )
        ),
        tags: ['Team'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns the newly created counter',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'Message'),
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Team'),
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
            new OA\Response(
                response: 404,
                description: 'Team not found'
            )
        ]
    )]
    public function create(Request $request, TeamService $teamService, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $params = new CreateTeamParams();
        $params->name = $data['name'];

        $errors = $validator->validate($params);
        if (count($errors) > 0) {
            return $this->json(['message' => 'Validation failed', 'errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $team = $teamService->createTeam($params->name);
        return $this->json([
            'message' => 'Team has been created successfully',
            'id' => $team->getId(),
            'name' => $team->getName()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{teamId}/total-steps', methods: ['GET'])]
    #[OA\Get(
        operationId: 'getTotalStepsByTeam',
        summary: 'Get the total steps taken by a team',
        tags: ['Team'],
        parameters: [
            new OA\Parameter(
                name: 'teamId',
                description: 'The ID of the team',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Total steps for the team',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'totalSteps', type: 'integer', example: 12345)
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Team not found'
            )
        ]
    )]
    public function getTotalSteps(TeamService $teamService, int $teamId): Response
    {
        $totalSteps = $teamService->getTotalStepsByTeam($teamId);

        return $this->json([
            'totalSteps' => $totalSteps
        ]);
    }

    #[Route('/{teamId}', methods: ['DELETE'])]
    #[OA\Delete(
        operationId: 'deleteTeam',
        summary: 'Delete a team',
        tags: ['Team'],
        parameters: [
            new OA\Parameter(
                name: 'teamId',
                description: 'The ID of the team to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Team deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Team not found'
            )
        ]
    )]
    public function delete(int $teamId, TeamService $teamService): Response
    {
        $teamService->deleteTeam($teamId);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
