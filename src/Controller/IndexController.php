<?php

namespace App\Controller;

use App\Service\InvalidWebsiteException;
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
        return $this->render('index.html.twig', [
            'website' => $request->query->get('website', 'duckduckgo.com')
        ]);
    }

    #[Route('/check', name: 'app_check', methods: ['GET', 'HEAD'])]
    #[Route('/{website}', name: 'app_check_vanity', requirements: ['website' => '[^/]+'], methods: ['GET', 'HEAD'])]
    public function check(WebsiteStatusService $websiteStatusService, Request $request, ?string $website): Response
    {
        $website = $request->query->get('website', $website);

        // We need a website to check, someone has probably visited /check directly.
        if (!$website) {
            throw $this->createNotFoundException();
        }

        try {
            $response = $websiteStatusService->getStatus($website);
        } catch (InvalidWebsiteException) {
            return $this->render('website-invalid.html.twig', [
                'website' => $website,
            ]);
        }

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
