<?php

namespace App\Service;

use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectService
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    // Create a new project
    public function createProject($project): Project
    {

        $errors = $this->validator->validate($project);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \Exception(implode(",", $errorMessages), Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $project;
    }

    // Find a project by its ID
    public function getProject(string $id): Project|null
    {
        return $this->entityManager->getRepository(Project::class)->find($id);
    }


    // Find all
    public function getProjects(): array
    {
        return $this->entityManager->getRepository(Project::class)->findAll();
    }

    // Update a project
    public function updateProject($id, $updatedProject): Project
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw new \Exception('Project not found', Response::HTTP_NOT_FOUND);
        }

        $project->setTitle($updatedProject->getTitle());
        $project->setDescription($updatedProject->getDescription());
        $project->setStatus($updatedProject->getStatus());
        $project->setDuration($updatedProject->getDuration());
        $project->setClient($updatedProject->getClient());
        $project->setCompany($updatedProject->getCompany());
        $project->setTasks($updatedProject->getTasks());
        $project->setClient($updatedProject->getClient());

        $errors = $this->validator->validate($project);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \Exception(implode(",", $errorMessages), Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        return $project;
    }

    // Delete a project
    public function deleteProject(string $id): bool
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        if (!$project) {
            return false;
        }
        $project->setDeletedAt(new \DateTimeImmutable());
        foreach ($project->getTasks() as $task) {
            $task->setDeletedAt(new \DateTimeImmutable());
        }
        $this->entityManager->flush();
        return true;
    }
}
