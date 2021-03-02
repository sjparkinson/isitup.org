<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApoControllerTest extends WebTestCase
{
    /**
     * @dataProvider validWebsites
     */
    public function testJsonWithValidWebsite($website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.json");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('application/json', $client->getResponse()->headers->get('content-type'));
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testJsonWithInvalidWebsite($website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.json");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider validWebsites
     */
    public function testTxtWithValidWebsite($website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.txt");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('text/plain', $client->getResponse()->headers->get('content-type'));
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testTxtWithInvalidWebsite($website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.txt");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function validWebsites()
    {
        return [
            ['example.com'],
            ['93.184.216.34'],
            ['2606:2800:220:1:248:1893:25c8:1946'],
        ];
    }

    public function invalidWebsites()
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