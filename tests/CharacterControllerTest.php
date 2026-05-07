<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CharacterControllerTest extends WebTestCase
{
    private $client;

    private $content; // Contenu de la réponse

    private static $identifier; // Identifier du Character

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    // Tests creates
    public function testCreate()
    {
        $this->client->request(
            'POST',
            '/characters/',
            [],// Parameters
            [],// Files
            ['CONTENT_TYPE' => 'application/json'],// Server
            <<<JSON
            {
            "kind": "Dame",
            "name": "Anardil",
            "surname": "Amie du soleil",
            "caste": "Magicien",
            "knowledge": "Sciences",
            "intelligence": 180,
            "strength": 180,
            "image": "/dames/anardil.webp"
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
        $this->client->request('GET', '/characters/' . self::$identifier);

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

    // Tests index
    public function testIndex()
    {
        // Tests with default values
        $this->client->request('GET', '/characters/');
        $this->assertResponseCode(200);
        $this->assertJsonResponse();
        // Tests with page
        $this->client->request('GET', '/characters/?page=1');
        $this->assertResponseCode(200);
        $this->assertJsonResponse();
        // Tests with page and size
        $this->client->request('GET', '/characters/?page=1&size=1');
        $this->assertResponseCode(200);
        $this->assertJsonResponse();
        // Tests with size
        $this->client->request('GET', '/characters/?size=1');
        $this->assertResponseCode(200);
        $this->assertJsonResponse();
    }

    public function testBadIdentifier()
    {
        $this->client->request('GET', '/characters/badIdentifier');

        $this->assertError404();
    }

    public function assertError404()
    {
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testInexistingIdentifier()
    {
        $this->client->request('GET', '/characters/0c9bf14db58689f35d4dcbd18a04b3078a6c3a15d');
        $this->assertError404();
    }

    public function testUpdate()
    {
        // Tests with partial data array
        $this->client->request(
            'PUT',
            '/characters/' . self::$identifier,
            [],// Parameters
            [],// Files
            ['CONTENT_TYPE' => 'application/json'],// Server
            <<<JSON
            {
            "kind": "Seigneur",
            "name": "Gorthol",
            "surname": "Heaume de terreur",
            "caste": "Chevalier",
            "knowledge": "Diplomatie",
            "intelligence": 140,
            "strength": 140,
            "image": "/seigneurs/gorthol.jpg"
            }
            JSON
        );
        $this->assertResponseCode(204);
    }

    public function testDelete()
    {
        $this->client->request('DELETE', '/characters/' . self::$identifier);
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

    // Asserts that Response code is equal to $code
    public function assertResponseCode(int $code)
    {
        $response = $this->client->getResponse();
        $this->assertEquals($code, $response->getStatusCode());
    }
}
