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
        $client->request('GET', '/api/docs');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('@context', $responseData);
        $this->assertArrayHasKey('@id', $responseData);
        $this->assertArrayHasKey('@type', $responseData);
        $this->assertArrayHasKey('title', $responseData);
        $this->assertArrayHasKey('entrypoint', $responseData);
        $this->assertArrayHasKey('supportedClass', $responseData);
    }
}
