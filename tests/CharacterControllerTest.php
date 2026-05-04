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

    public function testCreate(): void
    {
        $this->client->request('POST', '/characters/');
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
        $this->client->request('GET', '/characters/');
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
        $this->client->request('PUT', '/characters/' . self::$identifier);
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
