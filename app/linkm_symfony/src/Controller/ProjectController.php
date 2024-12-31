<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectController extends AbstractController
{
    private $projectService;
    private $serializer;

    public function __construct(ProjectService $projectService, SerializerInterface $serializer)
    {
        $this->projectService = $projectService;
        $this->serializer = $serializer;
    }

    #[Route('/project', name: 'add_project',  methods: ['POST'])]
    public function addProject(Request $request): JsonResponse
    {
        try {
            $jsonContent = $request->getContent();
            $project = $this->projectService->createProject($this->serializer->deserialize($jsonContent, Project::class, 'json'));
            if ($project->getId()) {
                return new JsonResponse(['message' => 'Project created successfully with ID: ' . $project->getId()], Response::HTTP_OK);
            } else {
                throw new \Exception('Project creation failed: No ID generated.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            throw new HttpException(
                $e->getCode(),
                $e->getMessage()
            );
        }
    }

    #[Route('/project/{id}', name: 'soft_delete_project',  methods: ['DELETE'])]
    public function softDeleteProject($id): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_OK);
            }
            if ($this->projectService->deleteProject($id)) {
                return new JsonResponse(['message' => 'Project deleted successfully'], Response::HTTP_OK);
            } else {
                throw new \Exception('Project not found', Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            throw new HttpException(
                $e->getCode(),
                $e->getMessage()
            );
        }
    }

    #[Route('/projects', name: 'get_projects',  methods: ['GET'])]
    public function getProjects(): JsonResponse
    {
        try {
            $json = $this->serializer->serialize($this->projectService->getProjects(), 'json', ['groups' => ['project:read', 'task:read']]);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            throw new HttpException(
                $e->getCode(),
                $e->getMessage()
            );
        }
    }


    #[Route('/project/{id}', name: 'get_project',  methods: ['GET'])]
    public function getProject($id): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_OK);
            }
            $project = $this->projectService->getProject($id);
            if ($project === null) {
                throw new \Exception('Project not found', Response::HTTP_NOT_FOUND);
            } else {
                $json = $this->serializer->serialize($project, 'json', ['groups' => ['project:read', 'task:read']]);
                return new JsonResponse($json, Response::HTTP_OK, [], true);
            }
        } catch (\Exception $e) {
            throw new HttpException(
                $e->getCode(),
                $e->getMessage()
            );
        }
    }


    #[Route('/project/{id}', name: 'edit_project',  methods: ['PUT'])]
    public function editProject($id, Request $request): JsonResponse
    {
        try {

            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_OK);
            }

            $jsonContent = $request->getContent();
            $updatedProject = $this->serializer->deserialize($jsonContent, Project::class, 'json');
            $project = $this->projectService->updateProject($id, $updatedProject);
            return new JsonResponse(['message' => 'Project edited successfully with ID: ' . $project->getId()], Response::HTTP_OK);
        } catch (\Exception $e) {
            throw new HttpException(
                $e->getCode(),
                $e->getMessage()
            );
        }
    }
}
