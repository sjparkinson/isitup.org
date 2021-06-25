<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\InvalidWebsiteException;
use App\Service\WebsiteStatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    #[Route('/', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        return $this->render('index.html.twig', ['website' => 'duckduckgo.com']);
    }

    #[Route('/', methods: ['POST'])]
    public function indexSubmit(Request $request): Response
    {
        /** @var string */
        $website = $request->request->filter('website', 'duckduckgo.com');
        $website = strtolower(preg_replace('/^[ \s]+|[ \s]+$|http(s)?:\/\/|\/(.*)/i', '', $website));

        if ('' === $website) {
            return $this->redirectToRoute(
                'app_index_check',
                ['website' => 'duckduckgo.com'],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->redirectToRoute(
            'app_index_check',
            ['website' => $website],
            Response::HTTP_SEE_OTHER
        );
    }

    #[Route('/{website}', requirements: ['website' => '[^/]+'], methods: ['GET', 'HEAD'])]
    public function check(WebsiteStatusService $websiteStatusService, Request $request, string $website): Response
    {
        try {
            $response = $websiteStatusService->getStatus($website);
        } catch (InvalidWebsiteException) {
            return $this->render('website-invalid.html.twig', [
                'website' => $website,
            ]);
        }

        if (1 === $response['status']) {
            return $this->render('website-okay.html.twig', [
                'website' => $website,
                'response_total_time' => $response['response_total_time'],
                'response_status_code' => $response['response_status_code'],
                'response_ip_address' => $response['response_ip_address'],
            ]);
        }

        return $this->render('website-not-okay.html.twig', [
            'website' => $website,
        ]);
    }

    #[Route('/isitup.org', methods: ['GET', 'HEAD'], priority: 1)]
    public function really(Request $request): Response
    {
        return $this->render('website-really.html.twig');
    }
}
