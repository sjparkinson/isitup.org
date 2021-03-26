<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class IndexControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndexClearsWebsiteCookie()
    {
        $client = static::createClient();
        $client->getCookieJar()->set(new Cookie('website', 'example.com', domain: 'localhost'));

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertBrowserNotHasCookie('website');
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