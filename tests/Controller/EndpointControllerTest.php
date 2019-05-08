<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EndpointControllerTest extends WebTestCase
{
    public function testUlid()
    {
        $client = self::createClient();

        $client->request('GET', '/ulid');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $data = json_decode($response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertArrayHasKey('ulid', $data);
    }
}
