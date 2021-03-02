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
     * @dataProvider validWebsites
     */
    public function testTxtWithValidWebsite($website)
    {
        $client = static::createClient();

        $client->request('GET', "/${website}.txt");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('text/plain', $client->getResponse()->headers->get('content-type'));
    }

    public function validWebsites()
    {
        return [
            ['example.com'],
        ];
    }
}