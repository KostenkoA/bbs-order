<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends Controller
{
    /**
     * @return Response
     */
    public function healthCheckAction(): Response
    {
        return new Response('Health: ok');
    }
}
