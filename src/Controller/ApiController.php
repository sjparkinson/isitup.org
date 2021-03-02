<?php

namespace App\Controller;

use App\Service\HttpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/{website}.json", requirements={"website": "[^/]+"}, priority=1)
     */
    public function checkWebsiteJson(HttpService $http, Request $request, string $website): Response
    {
        if (!$http->isValidWebsite($website)) {
            throw new BadRequestHttpException();
        }
        
        $response = $http->fetch($website);

        $payload = [
            "domain"        => $website,
            "port"          => 80,
            "status_code"   => $response["status"],
            "response_ip"   => $response["response_ip_address"],
            "response_code" => $response["response_status_code"],
            "response_time" => floatval(number_format($response["response_total_time"], 3))
        ];

        $response = new JsonResponse($payload);

        if ($request->query->has("callback")) {
            $response->setCallback($request->query->get("callback"));
        }

        return $response;
    }

    /**
     * @Route("/{website}.txt", requirements={"website": "[^/]+"}, priority=1)
     */
    public function checkWebsiteText(HttpService $http, string $website): Response
    {
        if (!$http->isValidWebsite($website)) {
            throw new BadRequestHttpException();
        }
        
        $response = $http->fetch($website);

        $results = [
            $website,
            80,
            $response["status"],
            $response["response_ip_address"],
            $response["response_status_code"],
            number_format($response["response_total_time"], 3)
        ];

        $response = new Response(implode(", ", $results));
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
