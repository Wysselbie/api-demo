<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class Api
{
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function healthCheck(): Response
    {
        return new Response('OK');
    }
}
