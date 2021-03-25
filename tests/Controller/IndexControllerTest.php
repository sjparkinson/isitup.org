<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider validWebsites
     */
    public function testCheckWebsiteWithVanityUrl(string $website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider validWebsites
     */
    public function testCheckWebsiteWithWebsiteParam(string $website)
    {
        $client = static::createClient();

        $client->request('GET', '/check', ['website' => $website]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testCheckWebsiteWithInvalidWebsite(string $website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html #container p', 'We need a valid domain to check!');
    }

    /**
     * @dataProvider validWebsites
     */
    public function testSaveWebsite(string $website)
    {
        $client = static::createClient();

        $client->request('GET', "/save/${website}");

        $cookieJar = $client->getCookieJar();
        $cookieValue = $cookieJar->get('website', domain: 'localhost')->getValue();

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/'));
        $this->assertEquals($website, $cookieValue);
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