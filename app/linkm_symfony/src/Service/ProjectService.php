<?php

namespace App\Service;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectService
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

    // Create a new project
    public function createProject($jsonContent): JsonResponse
    {
        $project = $this->serializer->deserialize($jsonContent, Project::class, 'json');
        $errors = $this->validator->validate($project);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($project);
        $this->entityManager->flush();

        if ($project->getId()) {
            return new JsonResponse(['message' => 'Project created successfully with ID: ' . $project->getId()], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'Project creation failed: No ID generated.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Find a project by its ID
    public function getProject(string $id): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        if ($project === null) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        } else {
            $json = $this->serializer->serialize($project, 'json', ['groups' => ['project:read', 'task:read']]);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        }
    }


    // Find all
    public function getProjects(): JsonResponse
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        if ($projects === null) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        } else {
            $json = $this->serializer->serialize($projects, 'json', ['groups' => ['project:read', 'task:read']]);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        }
    }

    // Update a project
    public function updateProject($id, $jsonContent): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if ($project === null) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $updatedProduct = $this->serializer->deserialize($jsonContent, Project::class, 'json');

        $project->setTitle($updatedProduct->getTitle());
        $project->setDescription($updatedProduct->getDescription());
        $project->setStatus($updatedProduct->getStatus());
        $project->setDuration($updatedProduct->getDuration());
        $project->setClient($updatedProduct->getClient());
        $project->setCompany($updatedProduct->getCompany());
        $project->setTasks($updatedProduct->getTasks());
        $project->setClient($updatedProduct->getClient());

        $errors = $this->validator->validate($project);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Project edited successfully with ID: ' . $project->getId()], Response::HTTP_OK);
    }

    // Delete a project
    public function deleteProject(string $id): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        if (!$project) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }
        $project->setDeletedAt(new \DateTimeImmutable());
        foreach ($project->getTasks() as $task) {
            $task->setDeletedAt(new \DateTimeImmutable());
        }
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Project deleted successfully'], Response::HTTP_OK);
    }
}
