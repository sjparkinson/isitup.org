<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\InvalidWebsiteException;
use App\Service\WebsiteStatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AppController extends AbstractController
{
    private const DEFAULT_WEBSITE = 'duckduckgo.com';

    #[Route('/', name: 'app_index', methods: ['GET', 'HEAD', 'POST'])]
    public function index(Request $request): Response
    {
        /** @var FormInterface */
        $form = $this->createFormBuilder(['website' => self::DEFAULT_WEBSITE])
            ->add('website', TextType::class, ['required' => false])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<array-key, string> */
            $data = $form->getData();

            $website = $data['website'] ?? self::DEFAULT_WEBSITE;
            $website = preg_replace('/^[ \s]+|[ \s]+$|http(s)?:\/\/|\/(.*)/i', '', $website);
            $website = strtolower($website);
            $website = '' === $website ? self::DEFAULT_WEBSITE : $website;

            return $this->redirectToRoute(
                'app_check',
                ['website' => $website],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{website}', name: 'app_check', requirements: ['website' => '[^/]+'], methods: ['GET', 'HEAD'])]
    public function check(WebsiteStatusService $websiteStatusService, Request $request, string $website): Response
    {
        try {
            $status = $websiteStatusService->getStatus($website);
        } catch (InvalidWebsiteException) {
            return $this->render('website-invalid.html.twig', [
                'website' => $website,
            ]);
        }

        if ($status->isOkay()) {
            return $this->render('website-okay.html.twig', [
                'website' => $website,
                'response_total_time' => $status->getResponseTime(),
                'response_status_code' => $status->getStatusCode(),
                'response_ip_address' => $status->getIpAddress(),
            ]);
        }

        return $this->render('website-not-okay.html.twig', [
            'website' => $website,
        ]);
    }

    #[Route('/isitup.org', name: 'app_really', methods: ['GET', 'HEAD'], priority: 1)]
    public function really(Request $request): Response
    {
        return $this->render('website-really.html.twig');
    }
}
