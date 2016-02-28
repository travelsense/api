<?php
namespace Api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HealthCheckController
{
    public function healthCheck(Request $request)
    {
        return JsonResponse::create([
            'check' => 'OK',
            'requestHeaders' => $request->headers->all()
        ]);
    }
}
