<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class WebsiteStatusFactory
{
    public static function createFromResponse(string $website, ResponseInterface $response): WebsiteStatus
    {
        $status = new WebsiteStatus($website);

        $status->setTotalTime((float) $response->getInfo('total_time'));
        $status->setStatusCode($response->getStatusCode());
        $status->setIpAddress((string) $response->getInfo('primary_ip'));

        return $status;
    }

    public static function createFromRedirectionException(string $website, RedirectionException $e): WebsiteStatus
    {
        $status = new WebsiteStatus($website);

        $response = $e->getResponse();

        $status->setTotalTime((float) $response->getInfo('total_time'));
        $status->setStatusCode($response->getStatusCode());
        $status->setIpAddress((string) $response->getInfo('primary_ip'));

        return $status;
    }
}
