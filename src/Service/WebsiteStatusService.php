<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\WebsiteStatus;
use App\Entity\WebsiteStatusFactory;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebsiteStatusService implements WebsiteStatusServiceInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getStatus(string $website): WebsiteStatus
    {
        if (!self::isValidWebsite($website)) {
            throw new InvalidWebsiteException("${website} is not a valid website");
        }

        try {
            $response = $this->httpClient->request('HEAD', "http://$website");

            // Wait for the request to finish by asking for the headers. This will
            // raise an exception if there are any issues with the network request.
            $response->getHeaders();

            return WebsiteStatusFactory::createFromResponse($website, $response);
        } catch (RedirectionException $e) {
            return WebsiteStatusFactory::createFromRedirectionException($website, $e);
        } catch (HttpExceptionInterface | TransportExceptionInterface) {
            return new WebSiteStatus($website);
        }
    }

    /**
     * Ensure that $website is a valid IP address or domain name.
     */
    private static function isValidWebsite(string $website): bool
    {
        $filterFlags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        // See if the input is a valid IP address.
        if (filter_var($website, FILTER_VALIDATE_IP, $filterFlags)) {
            return true;
        }

        // See if the input is a valid domain that resolves to an IPv4 address.
        if (preg_match("/^(xn--)?([\w0-9]([\w0-9\-]{0,61}[\w0-9])?\.)+(xn--)?[\w]{2,6}$/i", $website)) {
            return true;
        }

        return false;
    }
}
