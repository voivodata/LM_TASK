<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService
{
    private $entityManager;
    private $serializer;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    //Add task
    public function createTask($id, $jsonContent): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        $task = $this->serializer->deserialize($jsonContent, Task::class, 'json');
        $task->setProject($project);
        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        if ($task->getId()) {
            return new JsonResponse(['message' => 'Task created successfully with ID: ' . $task->getId()], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'Task creation failed: No ID generated.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Edit task 
    public function updateTask($id, $jsonContent): JsonResponse
    {

        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if ($task === null) {
            return new JsonResponse(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $updatedTask = $this->serializer->deserialize($jsonContent, Task::class, 'json');

        $task->setName($updatedTask->getName());

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Task edited successfully with ID: ' . $task->getId()], Response::HTTP_OK);
    }
    // Delete task 
    public function softDeleteTask($id): JsonResponse
    {

        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if (!$task) {
            return new JsonResponse(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }
        $task->setDeletedAt(new \DateTimeImmutable());

        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Task deleted successfully'], Response::HTTP_OK);
    }
}
