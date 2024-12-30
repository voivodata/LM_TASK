<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Ramsey\Uuid\Doctrine\UuidGenerator as DoctrineUuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: DoctrineUuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['task:read'])]
    private $id;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['task:read'])]
    #[Assert\Length(
        min: 3,
        minMessage: "The task name must be at least {{ limit }} characters long."
    )]
    #[Assert\NotBlank(
        message: "The task name is required."
    )]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['task:read'])]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project;

    public function getId(): ?LazyUuidFromString
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
