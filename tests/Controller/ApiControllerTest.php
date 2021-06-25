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

        $contentType = $client->getResponse()->headers->get('content-type');
        $content = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertNotNull($contentType);
        /* @psalm-suppress PossiblyNullArgument */
        $this->assertStringContainsString('application/json', $contentType);

        $this->assertNotFalse($content);
        $this->assertJson($content);
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

        $contentType = $client->getResponse()->headers->get('content-type');
        $content = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();

        $this->assertNotNull($contentType);
        /* @psalm-suppress PossiblyNullArgument */
        $this->assertStringContainsString('text/plain', $contentType);

        $this->assertNotFalse($content);
        /* @psalm-suppress PossiblyNullArgument */
        $this->assertMatchesRegularExpression("/\S+, \S+, \S+, \S*, \S*, \S+/", $content);
        $this->assertStringStartsWith($website, $content);
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

    /**
     * @return list<list<string>>
     */
    public function validWebsites(): array
    {
        return [
            ['example.com'],
            ['93.184.216.34'],
            ['2606:2800:220:1:248:1893:25c8:1946'],
        ];
    }

    /**
     * @return list<list<string>>
     */
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
