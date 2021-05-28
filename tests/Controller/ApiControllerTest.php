<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    /**
     * @dataProvider validWebsites
     */
    public function testJsonWithValidWebsite(string $website): void
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.json");

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJson($client->getResponse()->getContent());
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testJsonWithInvalidWebsite(string $website): void
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.json");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider validWebsites
     */
    public function testTxtWithValidWebsite(string $website): void
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.txt");

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('text/plain', $client->getResponse()->headers->get('content-type'));
        $this->assertMatchesRegularExpression("/\S+, \S+, \S+, \S*, \S*, \S+/", $client->getResponse()->getContent());
        $this->assertStringStartsWith($website, $client->getResponse()->getContent());
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testTxtWithInvalidWebsite(string $website): void
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.txt");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function validWebsites(): array
    {
        return [
            ['example.com'],
            ['93.184.216.34'],
            ['2606:2800:220:1:248:1893:25c8:1946'],
        ];
    }

    public function invalidWebsites(): array
    {
        return [
            ['this-is-an-invalid-website'],
            ['this-is-an-invalid-website.c'],
            ['-example.com'],
            ['example.com-'],
            ['127.0.0.1'],
            ['10.0.0.0'],
            ['127.0.0.'],
        ];
    }
}
