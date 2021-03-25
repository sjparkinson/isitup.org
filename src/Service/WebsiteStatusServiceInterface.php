<?php

namespace App\Service;

interface WebsiteStatusServiceInterface
{
    public function isValidWebsite(string $website): bool;

    public function getStatus(string $website): array;
}