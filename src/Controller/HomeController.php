<?php

namespace App\Controller;

use App\Mercure\CookieGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    #[Route('/', name:'app_home')]
    public function index(CookieGenerator $cookieGenerator): Response
    {
        $token = $cookieGenerator->generate($this->getUser()->getUserIdentifier());
        $response = $this->render('home/index.html.twig');
        $response->headers->setCookie(
            new Cookie(
                'mercureAuthorization',
                $token,
                (new \DateTime())->add(new \DateInterval('PT2H')),
                '/.well-known/mercure',
                null,
                false,
                true,
                false,
                'strict'
            )
        );

        return $response;
    }

    #[Route('/ping', name: 'ping', methods: ['POST'])]
    public function ping(HubInterface $hub)
    {
        $update = new Update('http://example.com/ping', "[]");
        $hub->publish($update);
        return new Response('published!');
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    #[Route('/test')]
    public function test()
    {
        return $this->httpClient->request('POST', 'https://localhost:443/.well-known/mercure', [

        ])->getContent();
    }
}