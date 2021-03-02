<?php

namespace App\Tests\Service;

use App\Service\HttpService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpServiceTest extends TestCase
{
    /**
     * @dataProvider validWebsites
     */
    public function testIsValidWebsiteWithValidWebsite($website)
    {
        $httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $httpService = new HttpService($httpClient);

        $this->assertTrue($httpService->isValidWebsite($website));
    }
    
    /**
     * @dataProvider invalidWebsites
     */
    public function testIsValidWebsiteWithInvalidWebsite($website)
    {
        $httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $httpService = new HttpService($httpClient);

        $this->assertFalse($httpService->isValidWebsite($website));
    }

    public function validWebsites()
    {
        return [
            ['example.com'],
            ['duckduckgo.com'],
            ['isitup.org'],
            ['xn--c1yn36f.com'],
        ];
    }

    public function invalidWebsites()
    {
        return [
            ['-example.com'],
            ['example.com-'],
            ['example'],
            ['example.c'],
        ];
    }
}