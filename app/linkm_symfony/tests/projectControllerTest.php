<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ProjectControllerTest extends WebTestCase
{

    static $jwtToken;
    static $projectId;
    static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = self::createClient();
        self::bootKernel();
    }



    public function testMigrationsCreate()
    {
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application(self::$kernel);

        $application->setAutoExit(false);

        $output = new NullOutput();

        $inputCreate = new ArrayInput([
            'command' => 'doctrine:database:create',
            '--no-interaction' => true,
        ]);

        $application->run($inputCreate, $output);

        $inputMigrate = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '--no-interaction' => true,
        ]);

        $application->run($inputMigrate, $output);

        $this->assertTrue(true, 'Migrations were applied successfully.');
    }


    public function testUserCreate()
    {
        $client = self::$client;
        $client->request('POST', '/api/user', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'username' => 'test',
            'password' => '123',
        ]));
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'username' => 'test',
            'password' => '123',
        ]));
        $data = json_decode($client->getResponse()->getContent(), true);
        static::$jwtToken = $data['token'];
        $this->assertArrayHasKey('token', $data);
    }

    public function testCreateProject()
    {
        $client = self::$client;
        $client->request('POST', '/api/project', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_Authorization' => 'Bearer ' . static::$jwtToken,
        ], json_encode([
            'title' => 'testTitle',
            'status' => 'inprogress',
            'duration' => 'duration',
            'company' => 'company',
        ]));

        $response = $client->getResponse()->getContent();
        $data = json_decode($response, true);
        preg_match('/ID:\s([a-f0-9\-]+)/', $data['message'], $matches);
        static::$projectId = $matches[1];  // This is the extracted ID
        $this->assertNotNull(static::$projectId);
    }
    public function testGetProjectWithValidId()
    {

        $client = self::$client;
        $client->request('GET', '/api/project/' . static::$projectId, [], [], [
            'HTTP_Authorization' => 'Bearer ' . static::$jwtToken,
        ]);
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(static::$projectId, $data['id']);
    }


    public function testDropDB(): void
    {
        $output = new NullOutput();

        self::bootKernel();
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application(self::$kernel);

        $application->setAutoExit(false);

        $inputDrop = new \Symfony\Component\Console\Input\ArrayInput([
            'command' => 'doctrine:database:drop',
            '--force' => true,
        ]);

        $application->run($inputDrop, $output);
        $this->assertTrue(true, 'Db dropped');
    }
}
