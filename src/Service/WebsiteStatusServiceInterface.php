<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\WebsiteStatus;

interface WebsiteStatusServiceInterface
{
    /**
     * @throws InvalidWebsiteException
     */
    public function getStatus(string $website): WebsiteStatus;
}
