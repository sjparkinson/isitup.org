<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider validIndexWebsites
     */
    public function testIndexSubmittedWithValidWebsite(string $website, string $expectedWebsite): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $client->submitForm('submit', [
            'website' => $website,
        ]);

        $this->assertResponseRedirects("/${expectedWebsite}", 303);
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testIndexSubmittedWithInvalidWebsite(string $website): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $client->submitForm('submit', [
            'website' => $website,
        ]);

        $this->assertResponseRedirects("/${website}", 303);
    }

    /**
     * @dataProvider validWebsites
     */
    public function testCheckWithValidWebsite(string $website): void
    {
        $client = static::createClient();

        $client->request('GET', "/${website}");

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testCheckWebsiteWithInvalidWebsite(string $website): void
    {
        $client = static::createClient();

        $client->request('GET', "/${website}");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html #container p', 'We need a valid domain to check!');
    }

    public function validIndexWebsites(): array
    {
        return [
            ['example.com', 'example.com'],
            ['93.184.216.34', '93.184.216.34'],
            ['https://93.184.216.34', '93.184.216.34'],
            ['2606:2800:220:1:248:1893:25c8:1946', '2606:2800:220:1:248:1893:25c8:1946'],
            ['https://2606:2800:220:1:248:1893:25c8:1946', '2606:2800:220:1:248:1893:25c8:1946'],
            ['http://example.com', 'example.com'],
            ['https://example.com', 'example.com'],
            ['https://example.com/path/', 'example.com']
        ];
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
            ['127.0.0.']
        ];
    }
}
