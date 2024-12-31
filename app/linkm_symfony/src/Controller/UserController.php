<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private $userService;
    private $serializer;
    public function __construct(UserService $userService, SerializerInterface $serializer)
    {
        $this->userService = $userService;
        $this->serializer = $serializer;
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): void {}

    #[Route('/testexeption', name: 'test_exeption', methods: ['GET'])]
    public function testExeption(): JsonResponse
    {
        throw new \Exception('test exeption error', 500);
    }


    #[Route('/user', name: 'add_user', methods: ['POST'])]
    public function addUser(
        Request $request,
    ): JsonResponse {
        try {
            $jsonContent = $request->getContent();
            $user = $this->serializer->deserialize($jsonContent, User::class, 'json');
            if ($this->userService->createUser($user)->getId()) {
                return new JsonResponse(['message' => 'User created successfully'], Response::HTTP_CREATED);
            } else {
                throw new \Exception('Project creation failed: No ID generated.', Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            throw new \Exception('User create failed: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
