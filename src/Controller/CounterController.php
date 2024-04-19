<?php

declare(strict_types=1);

namespace App\Controller;

use App\OutputModel\UnifyOutputModel;
use App\Params\CreateCounterParams;
use App\Params\CounterParams;
use App\Service\CounterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;


#[Route('/api/counter')]
class CounterController extends AbstractController
{
    #[Route('/team/{teamId}', methods: ['POST'])]
    #[OA\Post(
        operationId: 'addCounter',
        summary: 'Add a counter to a specified team',
        requestBody: new OA\RequestBody(
            description: 'Payload to create a new counter',
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'stepCount', type: 'integer', example: 0)
                    ],
                    type: 'object'
                )
            )
        ),
        tags: ['Counter'],
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
                            new OA\Property(property: 'teamId', type: 'integer', example: 1),
                            new OA\Property(property: 'stepCount', type: 'integer', example: 0),
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input, object invalid'
            ),
            new OA\Response(
                response: 404,
                description: 'Team not found'
            )
        ]
    )]
    public function create(
        Request $request,
        int $teamId,
        ValidatorInterface $validator,
        CounterService $counterService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $counterParams = new CreateCounterParams();
        $counterParams->stepCount = $data['stepCount'];
        $counterParams->name = $data['name'];

        if (count($errors = $validator->validate($counterParams)) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $counter = $counterService->createCounter($teamId, $counterParams);

        return $this->json([
            'message' => 'Counter has been added successfully',
            'id' => $counter->getId(),
            'teamId' => $counter->getTeam()->getId(),
            'name' => $counter->getName(),
            'stepCount' => $counter->getStepCount()
        ]);
    }

    #[Route('/{counterId}/increment', methods: ['POST'])]
    #[OA\Post(
        path: '/api/counter/{counterId}/increment',
        operationId: 'incrementCounter',
        summary: 'Increment the step count of a counter',
        requestBody: new OA\RequestBody(
            description: 'Data containing the number of steps to add',
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['stepCount'],
                    properties: [
                        new OA\Property(property: 'stepCount', type: 'integer', example: 10)
                    ],
                    type: 'object'
                )
            )
        ),
        tags: ['Counter'],
        parameters: [
            new OA\Parameter(
                name: 'counterId',
                description: 'The ID of the counter to be incremented',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Counter incremented successfully',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'Counter incremented successfully'),
                            new OA\Property(property: 'counterId', type: 'integer', example: 1),
                            new OA\Property(property: 'stepCount', type: 'integer', example: 120)
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input, object invalid'
            ),
            new OA\Response(
                response: 404,
                description: 'Counter not found'
            )
        ]
    )]
    public function increment(
        Request $request,
        CounterService $counterService,
        int $counterId,
        ValidatorInterface $validator,
    ): Response {
        $data = json_decode($request->getContent(), true);

        $counterParams = new CounterParams();
        $counterParams->stepCount = $data['stepCount'];

        if (count($errors = $validator->validate($counterParams)) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $counter = $counterService->incrementCounter($counterId, $counterParams->stepCount);

        return $this->json([
            'message' => 'Counter incremented successfully',
            'counterId' => $counter->getId(),
            'stepCount' => $counter->getStepCount()
        ]);
    }

    #[Route('/team/{teamId}', methods: ['GET'])]
    #[OA\Get(
        operationId: 'listCountersByTeam',
        summary: 'List all counters in a team',
        tags: ['Counter'],
        parameters: [
            new OA\Parameter(
                name: 'teamId',
                description: 'The ID of the team to retrieve counters for',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful retrieval of counters',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: UnifyOutputModel::class))
                    )
                )
            ),
            new OA\Response(response: 404, description: 'Team not found')
        ]
    )]
    public function listCountersByTeam(int $teamId, CounterService $counterService): Response
    {
        return $this->json($counterService->getCountersByTeam($teamId));
    }

    #[Route('/{counterId}', methods: ['DELETE'])]
    #[OA\Delete(
        operationId: 'deleteCounter',
        summary: 'Delete a counter',
        tags: ['Counter'],
        parameters: [
            new OA\Parameter(
                name: 'counterId',
                description: 'The ID of the counter to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Counter deleted successfully'
            ),
            new OA\Response(
                response: 404,
                description: 'Counter not found'
            )
        ]
    )]
    public function deleteCounter(int $counterId, CounterService $counterService): Response
    {
        $counterService->deleteCounter($counterId);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
