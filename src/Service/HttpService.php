<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class HttpService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Checks if the given string is a valid IP address or a domain name.
     */
    public function isValidWebsite(string $website)
    {
        // See if the input is a valid IP address.
        if (filter_var($website, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        }

        // See if the input is a valid domain that resolves to an IPv4 address.
        if (preg_match("/^(xn--)?([\w0-9]([\w0-9\-]{0,61}[\w0-9])?\.)+(xn--)?[\w]{2,6}$/i", $website)) {
            return true;
        }

        return false;
    }

    /**
     * See if the website is doing something like working.
     */
    public function fetch(string $website)
    {
        try {
            $response = $this->httpClient->request('HEAD', "http://$website");

            // Wait for the request to finish.
            $response->getHeaders();
        } catch (RedirectionException $e) {
            return [
                "status" => 1,
                "response_total_time" => $response->getInfo('total_time'),
                "response_status_code" => $response->getStatusCode(),
                "response_ip_address" => $response->getInfo('primary_ip')
            ];
        } catch (HttpExceptionInterface) {
            return [
                "status" => 2
            ];
        } catch (TransportExceptionInterface) {
            return [
                "status" => 2
            ];
        }

        if (in_array($response->getStatusCode(), [200, 301, 302, 303, 304, 307, 308, 400, 401, 403, 405])) {
            return [
                "status" => 1,
                "response_total_time" => $response->getInfo('total_time'),
                "response_status_code" => $response->getStatusCode(),
                "response_ip_address" => $response->getInfo('primary_ip')
            ];
        }

        return [
            "status" => 2,
        ];
    }
}
