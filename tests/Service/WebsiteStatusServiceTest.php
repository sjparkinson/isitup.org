<?php

namespace App\Tests\Service;

use App\Service\InvalidWebsiteException;
use App\Service\WebsiteStatusService;
use App\Service\WebsiteStatusServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebsiteStatusServiceTest extends TestCase
{
    private WebsiteStatusServiceInterface $websiteStatusService;

    protected function setUp(): void
    {
        $httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $this->websiteStatusService = new WebsiteStatusService($httpClient);
    }

    /**
     * @dataProvider validWebsites
     */
    public function testWithValidWebsite(string $website): void
    {
        $this->assertIsArray($this->websiteStatusService->getStatus($website));
    }

    /**
     * @dataProvider invalidWebsites
     */
    public function testWithInvalidWebsite(string $website): void
    {
        $this->expectException(InvalidWebsiteException::class);
        $this->websiteStatusService->getStatus($website);
    }

    public function validWebsites(): array
    {
        return [
            ['example.com'],
            ['duckduckgo.com'],
            ['isitup.org'],
            ['xn--c1yn36f.com'],
            ['93.184.216.34'],
            ['2606:2800:220:1:248:1893:25c8:1946'],
        ];
    }

    public function invalidWebsites(): array
    {
        return [
            ['-example.com'],
            ['example.com-'],
            ['example'],
            ['example.c'],
            ['127.0.0.1'],
            ['10.0.0.0'],
            ['127.0.0.']
        ];
    }
}
