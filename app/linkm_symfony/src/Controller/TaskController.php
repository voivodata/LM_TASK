<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TaskController extends AbstractController
{
    private $taskService;
    private $serializer;

    public function __construct(TaskService $taskService, SerializerInterface $serializer)
    {
        $this->taskService = $taskService;
        $this->serializer = $serializer;
    }

    #[Route('/task/{id}', name: 'edit_task',  methods: ['PUT'])]
    public function editTask($id, Request $request): JsonResponse
    {
        try {

            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_OK);
            }

            $jsonContent = $request->getContent();
            $updatedTask = $this->serializer->deserialize($jsonContent, Task::class, 'json');
            $task = $this->taskService->updateTask($id, $updatedTask);
            return new JsonResponse(['message' => 'Task edited successfully with ID: ' . $task->getId()], Response::HTTP_OK);
        } catch (\Exception $e) {
            throw new \Exception('Task edit failed: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/task/{id}', name: 'soft_delete_task',  methods: ['DELETE'])]
    public function softDeleteTask($id): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_OK);
            }
            if ($this->taskService->softDeleteTask($id)) {
                return new JsonResponse(['message' => 'Task deleted successfully'], Response::HTTP_OK);
            } else {
                throw new \Exception('Task not found', Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            throw new \Exception('Task edit failed: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/task/{id}', name: 'create_task',  methods: ['post'])]
    public function createTask($id, Request $request): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper project id format'], Response::HTTP_OK);
            }
            $jsonContent = $request->getContent();
            $task = $this->serializer->deserialize($jsonContent, Task::class, 'json');
            if ($this->taskService->createTask($id, $task)->getId()) {
                return new JsonResponse(['message' => 'Task created successfully with ID: ' . $task->getId()], Response::HTTP_OK);
            } else {
                throw new \Exception('Task creation failed: No ID generated.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            throw new \Exception('Remove Task failed: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
