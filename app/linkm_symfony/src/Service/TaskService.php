<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
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
    public function createTask($id, $task): Task
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        $task->setProject($project);
        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \Exception(implode(",", $errorMessages), Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return $task;
    }
    // Edit task 
    public function updateTask($id, $updatedTask): Task
    {

        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            throw new \Exception('Task not found', Response::HTTP_NOT_FOUND);
        }

        $task->setName($updatedTask->getName());

        $errors = $this->validator->validate($task);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \Exception(implode(",", $errorMessages), Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return $task;
    }
    // Delete task 
    public function softDeleteTask($id): bool
    {

        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if (!$task) {
            return false;
        }
        $task->setDeletedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
        return true;
    }
}
