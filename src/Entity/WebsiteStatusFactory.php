<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class WebsiteStatusFactory
{
    public static function createFromResponse(ResponseInterface $response): WebsiteStatus
    {
        $status = new WebsiteStatus();

        $status->setTotalTime((float) $response->getInfo('total_time'));
        $status->setStatusCode($response->getStatusCode());
        $status->setIpAddress((string) $response->getInfo('primary_ip'));

        return $status;
    }

    public static function createFromRedirectionException(RedirectionException $e): WebsiteStatus
    {
        $status = new WebsiteStatus();

        $response = $e->getResponse();

        $status->setTotalTime((float) $response->getInfo('total_time'));
        $status->setStatusCode($response->getStatusCode());
        $status->setIpAddress((string) $response->getInfo('primary_ip'));

        return $status;
    }
}
