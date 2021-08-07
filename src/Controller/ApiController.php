<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\InvalidWebsiteException;
use App\Service\WebsiteStatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ApiController extends AbstractController
{
    #[Route('/{website}.json', name: 'app_api_json', requirements: ['website' => '[^/]+'], methods: ['GET', 'HEAD'], priority: 1)]
    public function jsonCheck(WebsiteStatusService $websiteStatusService, Request $request, string $website): JsonResponse
    {
        try {
            $status = $websiteStatusService->getStatus($website);
        } catch (InvalidWebsiteException) {
            throw new BadRequestHttpException();
        }

        $payload = [
            'domain' => $website,
            'port' => 80,
            'status_code' => $status->isOkay() ? 1 : 2,
            'response_ip' => $status->getIpAddress(),
            'response_code' => $status->getStatusCode(),
            'response_time' => floatval(number_format($status->getResponseTime(), 3)),
        ];

        $response = new JsonResponse($payload);

        if ($request->query->has('callback')) {
            $response->setCallback($request->query->get('callback'));
        }

        return $response;
    }

    #[Route('/{website}.txt', name: 'app_api_txt', requirements: ['website' => '[^/]+'], methods: ['GET', 'HEAD'], priority: 1)]
    public function textCheck(WebsiteStatusService $websiteStatusService, string $website): Response
    {
        try {
            $status = $websiteStatusService->getStatus($website);
        } catch (InvalidWebsiteException) {
            throw new BadRequestHttpException();
        }

        $results = [
            $website,
            80,
            $status->isOkay() ? 1 : 2,
            $status->getIpAddress(),
            $status->getStatusCode(),
            number_format($status->getResponseTime(), 3),
        ];

        $response = new Response(implode(', ', $results));
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
