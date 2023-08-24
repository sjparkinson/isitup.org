<?php

declare(strict_types=1);

namespace App\Entity;

final class WebsiteStatus
{
    private float $totalTime = 0.0;

    private ?int $statusCode = null;

    private ?string $ipAddress = null;

    public function getTotalTime(): float
    {
        return $this->totalTime;
    }

    public function setTotalTime(float $time): void
    {
        $this->totalTime = $time;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    public function isOkay(): bool
    {
        return in_array($this->getStatusCode(), [200, 301, 302, 303, 304, 307, 308]);
    }
}
