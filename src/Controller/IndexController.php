<?php

namespace App\Controller;

use App\Service\WebsiteStatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET', 'HEAD'])]
    public function index(Request $request): Response
    {
        $website = $request->query->has('website') ? $request->query->get('website') : 'duckduckgo.com';

        return $this->render('index.html.twig', [
            'website' => $website
        ]);
    }

    #[Route('/check', name: 'app_check', methods: ['GET', 'HEAD'])]
    #[Route('/{website}', name: 'app_check_vanity', requirements: ['website' => '[^/]+'], methods: ['GET', 'HEAD'])]
    public function check(WebsiteStatusService $websiteStatusService, Request $request, ?string $website): Response
    {
        if (!$website && !$request->query->get('website')) {
            // We need a value to check, someone has probably visited /check manually.
            throw $this->createNotFoundException();
        }

        // Pick the website to check from either the route parameter or the query parameter.
        $website = $website ?? $request->query->get('website');

        if (!$website || !$websiteStatusService->isValidWebsite($website)) {
            return $this->render('website-invalid.html.twig', [
                'website' => $website,
            ]);
        }

        $response = $websiteStatusService->getStatus($website);

        if ($response["status"] === 1) {
            return $this->render('website-okay.html.twig', [
                'website' => $website,
                'response_total_time' => $response["response_total_time"],
                'response_status_code' => $response["response_status_code"],
                'response_ip_address' => $response["response_ip_address"]
            ]);
        }

        return $this->render('website-not-okay.html.twig', [
            'website' => $website,
        ]);
    }
}
