<?php

declare(strict_types=1);

namespace App\Service;

interface WebsiteStatusServiceInterface
{
    /**
     * @return array<string, mixed>
     *
     * @throws InvalidWebsiteException
     */
    public function getStatus(string $website): array;
}
