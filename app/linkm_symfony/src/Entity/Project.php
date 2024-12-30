<?php

namespace App\Entity;

use App\Enum\ProjectStatus;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Doctrine\UuidGenerator as DoctrineUuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: DoctrineUuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['project:read'])]
    private $id;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', enumType: ProjectStatus::class, nullable: true)]
    #[Groups(['project:read'])]
    private ?ProjectStatus $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:read'])]
    private ?string $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:read'])]
    private ?string $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:read'])]
    private ?string $company = null;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null === $this->getCompany() && null === $this->getClient())
            $context->buildViolation('company or client are required')
                ->atPath('company')
                ->addViolation();
    }

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class, cascade: ['persist', 'remove'])]
    #[Groups(['project:read'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?LazyUuidFromString
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(?ProjectStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

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

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }



    public function setTasks(Collection $tasks): static
    {
        if (!$tasks->isEmpty()) {
            foreach ($this->tasks as $oldTask) {
                $oldTask->setDeletedAt(new \DateTimeImmutable());
            }
            foreach ($tasks as $task) {
                $this->tasks->add($task);
                $task->setProject($this);
            }
        }
        return $this;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }
}
