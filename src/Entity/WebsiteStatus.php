<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class WebsiteStatus
{
    private string $website;

    private float $response_time = 0.0;

    private ?int $status_code = null;

    private ?string $ip_address = null;

    public function __construct(string $website)
    {
        $this->website = $website;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response_time = (float) $response->getInfo('total_time');

        $this->status_code = $response->getStatusCode();

        $this->ip_address = (string) $response->getInfo('primary_ip');
    }

    public function setRedirectionException(RedirectionException $e): void
    {
        /* @var float */
        $this->response_time = (float) $e->getResponse()->getInfo('total_time');

        $this->status_code = $e->getResponse()->getStatusCode();

        /* @var string */
        $this->ip_address = (string) $e->getResponse()->getInfo('primary_ip');
    }

    public function getResponseTime(): float
    {
        return $this->response_time;
    }

    public function getStatusCode(): ?int
    {
        return $this->status_code;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function isOkay(): bool
    {
        return in_array($this->getStatusCode(), [200, 301, 302, 303, 304, 307, 308]);
    }
}
