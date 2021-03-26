<?php

namespace App\Controller;

use App\Service\WebsiteStatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $website = $request->query->has('website') ? $request->query->get('website') : 'duckduckgo.com';

        $response = $this->render('index.html.twig', [
            'website' => $website
        ]);

        $response->headers->clearCookie('website');

        return $response;
    }

    /**
     * @Route("/check")
     * @Route("/{website}", requirements={"website": "[^/]+"})
     */
    public function check(WebsiteStatusService $websiteStatusService, Request $request, ?string $website): Response
    {
        if (!$website && !$request->query->get('website')) {
            // We need a value to check, someone has probably visited /check manually.
            throw $this->createNotFoundException();
        }

        // Pick the website to check from either the route parameter or the query parameter.
        $website = $request->query->get('website') ?? $website;

        if (!$websiteStatusService->isValidWebsite($website)) {
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
