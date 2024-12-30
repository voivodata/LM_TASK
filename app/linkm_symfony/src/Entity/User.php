<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator as DoctrineUuidGenerator;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['username'], message: 'This username is already in use.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: DoctrineUuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "Username is required.")]
    #[Assert\Length(
        min: 3,
        minMessage: "The username must be at least {{ limit }} characters long."
    )]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Password is required.")]
    #[Assert\Length(
        min: 3,
        minMessage: "The password must be at least {{ limit }} characters long."
    )]
    private ?string $password = null;

    public function getId(): ?LazyUuidFromString
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
