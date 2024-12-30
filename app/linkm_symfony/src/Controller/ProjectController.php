<?php

namespace App\Controller;

use App\Service\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectController extends AbstractController
{
    private $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    #[Route('/project', name: 'add_project',  methods: ['POST'])]
    public function addProject(Request $request): JsonResponse
    {
        try {
            $jsonContent = $request->getContent();
            return $this->projectService->createProject($jsonContent);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Entity creation failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/project/{id}', name: 'soft_delete_project',  methods: ['DELETE'])]
    public function softDeleteProject($id): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_OK);
            }
            return $this->projectService->deleteProject($id);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Remove project failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/projects', name: 'get_projects',  methods: ['GET'])]
    public function getProjects(): JsonResponse
    {
        try {
            return $this->projectService->getProjects();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Retrieve project failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/project/{id}', name: 'get_project',  methods: ['GET'])]
    public function getProject($id): JsonResponse
    {
        try {
            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_OK);
            }
            return $this->projectService->getProject($id);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Retrieve project failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/project/{id}', name: 'edit_project',  methods: ['PUT'])]
    public function editProject($id, Request $request): JsonResponse
    {
        try {

            if (!\Ramsey\Uuid\Guid\Guid::isValid($id)) {
                return new JsonResponse(['message' => 'No propper id format'], Response::HTTP_BAD_REQUEST);
            }

            $jsonContent = $request->getContent();

            return $this->projectService->updateProject($id, $jsonContent);

            return new JsonResponse(['message' => 'Project edited successfully with ID: ' . $project->getId()], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Project edit failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
