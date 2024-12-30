<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private $entityManager;
    private $serializer;
    private $validator;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    // Create a new user
    public function createUser($jsonContent): JsonResponse
    {
        $user = $this->serializer->deserialize($jsonContent, User::class, 'json');
        $errors = $this->validator->validate($user);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['message' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($user->getId()) {
            return new JsonResponse(['message' => 'User created successfully'], Response::HTTP_CREATED);
        } else {
            return new JsonResponse(['message' => 'Project creation failed: No ID generated.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}