<?php

namespace App\Tests\Service;

use App\Entity\Project;
use App\Entity\Task;
use App\Enum\ProjectStatus;
use App\Service\ProjectService;
use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProjectServiceDBTest extends KernelTestCase
{

    private $validator;
    private $entityManager;
    private $projectService;

    protected function setUp(): void
    {

        $kernel = self::bootKernel();
        DatabasePrimer::prime($kernel);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->projectService = new ProjectService($this->entityManager, $this->validator);
    }

    public function testCreateProjectDB(): void
    {
        $task = new Task();
        $project = new Project();
        $project->setTitle('test Title');
        $project->setDescription('test Description');
        $project->setStatus(ProjectStatus::IN_PROGRESS);
        $project->setDuration('test Duration');
        $project->setClient('test Client');
        $project->setCompany('test Company');
        $project->addTask($task);
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        $projectRep = $this->entityManager->getRepository(Project::class);

        $record = $projectRep->findOneBy(['title' => 'test Title']);
        $this->assertEquals('test Title', $record->getTitle());
    }



    public function testDeleteProjectDB(): void
    {
        $task = new Task();
        $project = new Project();
        $project->setTitle('test Delete');
        $project->setDescription('test Description');
        $project->setStatus(ProjectStatus::IN_PROGRESS);
        $project->setDuration('test Duration');
        $project->setClient('test Client');
        $project->setCompany('test Company');
        $project->addTask($task);
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        $projectRep = $this->entityManager->getRepository(Project::class);
        $record = $projectRep->findOneBy(['title' => 'test Delete']);

        if ($this->projectService->deleteProject($record->getId())) {
            $deletedRecord = $projectRep->findOneBy(['title' => 'test Delete']);
            $this->assertNull($deletedRecord);
        } else {
            $this->fail('delete project failed');
        }
    }
    public function testUpdateProjectDB(): void
    {
        $task = new Task();
        $project = new Project();
        $project->setTitle('test Update');
        $project->setDescription('test Description');
        $project->setStatus(ProjectStatus::IN_PROGRESS);
        $project->setDuration('test Duration');
        $project->setClient('test Client');
        $project->setCompany('test Company');
        $project->addTask($task);
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        $projectRep = $this->entityManager->getRepository(Project::class);
        $record = $projectRep->findOneBy(['title' => 'test Update']);
        $project->setTitle('new Test Update');
        $newProject = $this->projectService->updateProject($record->getId(), $project);
        $this->assertEquals('new Test Update', $newProject->getTitle());
    }

    public function testGetProjectDB(): void
    {
        $task = new Task();
        $project = new Project();
        $project->setTitle('test Get Project');
        $project->setDescription('test Description');
        $project->setStatus(ProjectStatus::IN_PROGRESS);
        $project->setDuration('test Duration');
        $project->setClient('test Client');
        $project->setCompany('test Company');
        $project->addTask($task);
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        $projectRep = $this->entityManager->getRepository(Project::class);
        $record = $projectRep->findOneBy(['title' => 'test Get Project']);
        $getProject = $this->projectService->getProject($record->getId());
        $this->assertEquals('test Get Project', $getProject->getTitle());
    }
    public function testGetProjecstDB(): void
    {
        $task = new Task();
        $project = new Project();
        $project->setTitle('test Get Projects');
        $project->setDescription('test Description');
        $project->setStatus(ProjectStatus::IN_PROGRESS);
        $project->setDuration('test Duration');
        $project->setClient('test Client');
        $project->setCompany('test Company');
        $project->addTask($task);
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        $getProjectArray = $this->projectService->getProjects();
        $this->assertEquals('test Get Projects', $getProjectArray[0]->getTitle());
    }
}
