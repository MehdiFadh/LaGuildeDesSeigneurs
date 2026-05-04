<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CharacterControllerTest extends WebTestCase
{

    private $client;

    public function setUp(): void{
        $this->client = static::createClient();
    }

    public function testDisplay(): void
    {
        $this->client->request('GET', '/characters/a753af7d6527d26fdd0ddaff12d03729c8bcaba5');
        $this->assertJsonResponse();
    }

    public function assertJsonResponse()
    {
        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
    }

    // Tests index
    public function testIndex()
    {
        $this->client->request('GET', '/characters/');
        $this->assertJsonResponse();
    }

    public function testBadIdentifier(){
        $this->client->request('GET','/characters/badIdentifier');

        $this->assertError404();
    }

    public function assertError404(){
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testInexistingIdentifier(){
        $this->client->request('GET', '/characters/8f74f20597c5cf99dd42cd31331b7e6e2aeerror');
        $this->assertError404();
    }

}
