<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name:'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/ping', name: 'ping', methods: ['POST'])]
    public function ping(HubInterface $hub)
    {
        $update = new Update('http://example.com/ping', "[]");
        $hub->publish($update);
        return new Response('published!');
    }
}