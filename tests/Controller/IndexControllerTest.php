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
    public function testCheckWebsiteWithVanityUrl($website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider validWebsites
     */
    public function testCheckWebsiteWithWebsiteParam($website)
    {
        $client = static::createClient();

        $client->request('GET', '/check', ['website' => $website]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testCheckWebsiteWithInvalidWebsite($website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html #container p', 'We need a valid domain to check!');
    }

    /**
     * @dataProvider validWebsites
     */
    public function testSaveWebsite($website)
    {
        $client = static::createClient();

        $client->request('GET', "/save/${website}");

        $cookieJar = $client->getCookieJar();
        $cookieValue = $cookieJar->get('website', domain: 'localhost')->getValue();

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/'));
        $this->assertEquals($website, $cookieValue);
    }

    public function validWebsites()
    {
        return [
            ['example.com'],
        ];
    }

    public function invalidWebsites()
    {
        return [
            ['this-is-an-invalid-website'],
            ['this-is-an-invalid-website.c'],
            ['-example.com'],
            ['example.com-'],
        ];
    }
}