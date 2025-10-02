<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiHealthCheckTest extends WebTestCase
{
    public function testApiHealthCheck(): void
    {
        $client = static::createClient();

        // Test that the application boots properly
        $client->request('GET', '/api/health');

        // We expect the API Platform welcome page to be accessible
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
    }

    public function testApiDocumentation(): void
    {
        $client = static::createClient();

        // Test API documentation endpoint
        $client->request('GET', '/api/docs.json');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        $responseContent = $client->getResponse()->getContent();

        $this->assertNotFalse($responseContent);

        $responseData = json_decode($responseContent, true);

        $this->assertArrayHasKey('openapi', $responseData);
        $this->assertArrayHasKey('info', $responseData);
        $this->assertArrayHasKey('paths', $responseData);

        $this->assertArrayHasKey('title', $responseData['info']);
        $this->assertArrayHasKey('version', $responseData['info']);
        $this->assertArrayHasKey('description', $responseData['info']);
    }
}
