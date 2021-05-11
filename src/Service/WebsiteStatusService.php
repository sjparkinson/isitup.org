<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class WebsiteStatusService implements WebsiteStatusServiceInterface
{
    private HttpClientInterface $httpClient;

    /**
     * The list of HTTP status codes that indicate a website is working.
     */
    private const SUCCESS_STATUS_CODES = [200, 301, 302, 303, 304, 307, 308];

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getStatus(string $website): array
    {
        if (!self::isValidWebsite($website)) {
            throw new InvalidWebsiteException("${website} is not a valid website");
        }

        try {
            $response = $this->httpClient->request('HEAD', "http://$website");

            // Wait for the request to finish by asking for the headers. This will
            // raise an exception if there are any issues with the network request.
            $response->getHeaders();

            if (in_array($response->getStatusCode(), self::SUCCESS_STATUS_CODES)) {
                return [
                    "status" => 1,
                    "response_total_time" => $response->getInfo('total_time'),
                    "response_status_code" => $response->getStatusCode(),
                    "response_ip_address" => $response->getInfo('primary_ip')
                ];
            }
        } catch (RedirectionException $e) {
            return [
                "status" => 1,
                "response_total_time" => $e->getResponse()->getInfo('total_time'),
                "response_status_code" => $e->getResponse()->getInfo('http_code'),
                "response_ip_address" => $e->getResponse()->getInfo('primary_ip')
            ];
        } catch (HttpExceptionInterface | TransportExceptionInterface) {
            // Return the default response information.
        }

        return [
            "status" => 2,
            "response_total_time" => null,
            "response_status_code" => null,
            "response_ip_address" => null,
        ];
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
