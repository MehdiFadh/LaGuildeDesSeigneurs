<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BuildingControllerTest extends WebTestCase
{
    private $client;
    private $content;
    private static $identifier;

    private static $userId;

    public function setUp(): void
    {
        $this->client = static::createClient();
        // Récupération du User
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('contact@example.com');
        self::$userId = $testUser->getId();
        $this->client->loginUser($testUser);
    }

    public function testCreate(): void
    {
        $this->client->request(
            'POST',
            '/buildings/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            <<<JSON
            {
            "name": "Tour de Guet",
            "caste": "Garde",
            "strength": 50,
            "image": "/buildings/tour-guet.webp"
            }
            JSON
        );
        $this->assertResponseCode(201);
        $this->assertJsonResponse();
        $this->defineIdentifier();
        $this->assertIdentifier();
    }

    public function testDisplay(): void
    {
        $this->client->request('GET', '/buildings/'.self::$identifier);
        $this->assertResponseCode(200);
        $this->assertJsonResponse();
        $this->assertIdentifier();
    }

    public function assertJsonResponse()
    {
        $response = $this->client->getResponse();
        $this->content = json_decode($response->getContent(), true, 50);
        $this->assertResponseIsSuccessful();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
    }

    public function testIndex()
    {
        $this->client->request('GET', '/buildings/');
        $this->assertResponseCode(200);
        $this->assertJsonResponse();
    }

    public function testBadIdentifier()
    {
        $this->client->request('GET', '/buildings/badIdentifier');
        $this->assertError404();
    }

    public function assertError404()
    {
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testInexistingIdentifier()
    {
        $this->client->request('GET', '/buildings/0c9bf14db58689f35d4dcbd18a04b3078a6c3a15d');
        $this->assertError404();
    }

    public function testUpdate()
    {
        // Tests with whole content
        $this->client->request(
            'PUT',
            '/buildings/'.self::$identifier,
            [],// Parameters
            [],// Files
            ['CONTENT_TYPE' => 'application/json'],// Server
            <<<JSON
            {
            "name": "Château Oakenfield",
            "caste": "Erudit",
            "image": "/buildings/chateau-oakenfield.webp",
            "strength": 2000
            }
            JSON
        );
        $this->assertResponseCode(204);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/buildings/'.self::$identifier);
        $this->assertResponseCode(204);
    }

    public function assertIdentifier()
    {
        $this->assertArrayHasKey('identifier', $this->content);
        $this->assertArrayHasKey('modification', $this->content);
    }

    public function defineIdentifier()
    {
        self::$identifier = $this->content['identifier'];
    }

    public function assertResponseCode(int $code)
    {
        $response = $this->client->getResponse();
        $this->assertEquals($code, $response->getStatusCode());
    }

    public function testImages()
    {
        $this->client->request('GET', '/buildings/images');
        $this->assertJsonResponse();
        $this->client->request('GET', '/buildings/images/3');
        $this->assertJsonResponse();
    }
}
