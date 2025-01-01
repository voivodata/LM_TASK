<?php

namespace App\Tests\Service;

use App\Entity\Project;
use App\Entity\Task;
use App\Service\ProjectService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ProjectServiceTest extends KernelTestCase
{

    private $validator;
    private $entityManager;
    private $projectService;

    protected function setUp(): void
    {

        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->assertInstanceOf(EntityManagerInterface::class, $this->entityManager);
        $this->projectService = new ProjectService($this->entityManager, $this->validator);
    }




    public function testProjectValidatePersistFlush(): void
    {
        $project = new Project();

        $this->validator
            ->method('validate')
            ->with($project)
            ->willReturn(new ConstraintViolationList());

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($project);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->projectService->createProject($project);

        $this->assertSame($project, $result, 'The returned project should flush persist validate.');
    }

    public function testCreateProjectValidationFails(): void
    {
        $project = new Project();

        $violation = $this->createMock(ConstraintViolation::class);
        $violation
            ->method('getMessage')
            ->willReturn('Invalid project');

        $violations = new ConstraintViolationList([$violation]);

        $this->validator
            ->method('validate')
            ->with($project)
            ->willReturn($violations);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid project');
        $this->expectExceptionCode(Response::HTTP_BAD_REQUEST);

        $this->projectService->createProject($project);
    }

    public function testProjectViolation()
    {
        $entity = new Project();

        $context = $this->createMock(ExecutionContextInterface::class);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $context->expects($this->once())
            ->method('buildViolation')
            ->with('company or client are required')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())
            ->method('atPath')
            ->with('company')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $entity->validate($context);
    }


    public function testDeleteProjectNotFound()
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository->method('find')->willReturn(null);
        $this->entityManager->method('getRepository')->willReturn($repository);

        $result = $this->projectService->deleteProject('non-existent-id');
        $this->assertFalse($result);
    }


    public function testDeleteProjectWithTasks()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $project = $this->createMock(Project::class);
        $task = $this->createMock(Task::class);

        $tasksCollection = new ArrayCollection([$task]);
        $project->method('getTasks')->willReturn($tasksCollection);

        $repository->method('find')->willReturn($project);
        $this->entityManager->method('getRepository')->willReturn($repository);

        $uuid = new LazyUuidFromString('f47ac10b-58cc-4372-a567-0e02b2c3d479');
        $project->method('getId')->willReturn($uuid);

        $project->expects($this->once())->method('setDeletedAt');
        $task->expects($this->once())->method('setDeletedAt');
        $this->entityManager->expects($this->once())->method('flush');

        $result = $this->projectService->deleteProject($uuid);
        $this->assertTrue($result);
    }

    public function testGetProjectFound()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $project = $this->createMock(Project::class);

        $repository->method('find')->with('existing-id')->willReturn($project);
        $this->entityManager->method('getRepository')->willReturn($repository);


        $result = $this->projectService->getProject('existing-id');

        $this->assertSame($project, $result);
    }

    public function testGetProjectNotFound()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $repository->method('find')->with('non-existent-id')->willReturn(null);
        $this->entityManager->method('getRepository')->willReturn($repository);


        $result = $this->projectService->getProject('non-existent-id');

        $this->assertNull($result);
    }

    public function testUpdateProjectSuccess()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $project = new Project();
        $project->setTitle('Old Title');

        $updatedProject = new Project();
        $updatedProject->setTitle('New Title');

        $repository->method('find')->with('existing-id')->willReturn($project);
        $this->entityManager->method('getRepository')->willReturn($repository);

        $this->validator->method('validate')->willReturn(new ConstraintViolationList());


        $this->entityManager->expects($this->once())->method('persist')->with($updatedProject);
        $this->entityManager->expects($this->once())->method('flush');
        $result = $this->projectService->updateProject('existing-id', $updatedProject);
        $this->assertSame('New Title', $result->getTitle());
    }

    public function testUpdateProjectNotFound()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $repository->method('find')->with('non-existent-id')->willReturn(null);
        $this->entityManager->method('getRepository')->willReturn($repository);


        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Project not found');
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);

        $this->projectService->updateProject('non-existent-id', new Project());
    }

    public function testUpdateProjectValidationErrors()
    {
        $repository = $this->createMock(ObjectRepository::class);

        $project = new Project();

        $repository->method('find')->with('existing-id')->willReturn($project);
        $this->entityManager->method('getRepository')->willReturn($repository);

        $violation = new ConstraintViolation(
            'Title cannot be empty',
            null,
            [],
            '',
            'title',
            ''
        );
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([$violation]));


        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Title cannot be empty');
        $this->expectExceptionCode(Response::HTTP_BAD_REQUEST);

        $this->projectService->updateProject('existing-id', new Project());
    }
}
