<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): void {}

    #[Route('/testexeption', name: 'test_exeption', methods: ['GET'])]
    public function testExeption(): JsonResponse
    {
        throw new \Exception('test exeption error', 500);
    }


    #[Route('/adduser', name: 'add_user', methods: ['POST'])]
    public function addUser(
        Request $request,
    ): JsonResponse {
        try {
            $jsonContent = $request->getContent();
            return $this->userService->createUser($jsonContent);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User create failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
