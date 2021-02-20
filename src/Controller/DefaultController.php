<?php

namespace App\Controller;

use App\Service\HttpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $website = $request->cookies->get('website') ?? $request->query->get('website') ?? 'duckduckgo.com';

        return $this->render('index.html.twig', [
            'website' => $website,
            'has_saved_website' => $request->cookies->has('website')
        ]);
    }

    /**
     * @Route("/check")
     * @Route("/{website}", requirements={"website": "[^/]+"})
     */
    public function checkWebsite(HttpService $http, Request $request, ?string $website): Response
    {
        if (!$website && !$request->query->get('website')) {
            // We need a value to check, someone has probabily visited /check manually.
            throw $this->createNotFoundException();
        }

        // Pick the website to check from either the route paramater or the query parameter.
        $website = $request->query->get('website') ?? $website;

        if (!$http->isValidWebsite($website)) {
            return $this->render('invalid-website.html.twig', [
                'website' => $website,
            ]);
        }

        $response = $http->fetch($website);

        if ($response["status"] === 1) {
            return $this->render('okay-website.html.twig', [
                'website' => $website,
                'response_total_time' => $response["response_total_time"],
                'response_status_code' => $response["response_status_code"],
                'response_ip_address' => $response["response_ip_address"]
            ]);
        }

        return $this->render('not-okay-website.html.twig', [
            'website' => $website,
        ]);
    }

    /**
     * @Route("/{website}.json", requirements={"website": "[^/]+"}, priority=1)
     */
    public function checkWebsiteJson(HttpService $http, Request $request, string $website): Response
    {
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

    /**
     * @Route("/save/{website}", requirements={"website": "[^/]+"}, name="save")
     */
    public function setDefaultWebsite(string $website): Response
    {
        $response = $this->redirectToRoute('index');
        $response->headers->setCookie(Cookie::create('website', $website));

        return $response;
    }

    /**
     * Clear the `website` cookie.
     * 
     * @Route("/clear", name="clear", priority=1)
     */

    public function clearDefaultWebsite(): Response
    {
        $response = $this->redirectToRoute('index');
        $response->headers->clearCookie('website');

        return $response;
    }
}
