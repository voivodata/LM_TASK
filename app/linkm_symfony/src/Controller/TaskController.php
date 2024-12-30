<?php

namespace App\Controller;

use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('/task/{id}', name: 'edit_task',  methods: ['PUT'])]
    public function editTask($id, Request $request): JsonResponse
    {
        try {

            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_BAD_REQUEST);
            }

            $jsonContent = $request->getContent();

            return $this->taskService->updateTask($id, $jsonContent);

            return new JsonResponse(['message' => 'Task edited successfully with ID: ' . $task->getId()], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Task edit failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/task/{id}', name: 'soft_delete_task',  methods: ['DELETE'])]
    public function softDeleteTask($id): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_BAD_REQUEST);
            }
            return $this->taskService->softDeleteTask($id);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Remove Task failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/task/{id}', name: 'create_task',  methods: ['post'])]
    public function createTask($id, Request $request): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper project id format'], Response::HTTP_BAD_REQUEST);
            }
            $jsonContent = $request->getContent();
            return $this->taskService->createTask($id, $jsonContent);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Remove Task failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
