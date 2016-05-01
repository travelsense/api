<?php
namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;

class HealthCheckController extends ApiController
{
    /**
     * @param Request $request
     * @return array
     */
    public function healthCheck(Request $request): array
    {
        return [
            'check'          => 'OK',
            'requestHeaders' => $request->headers->all(),
            'env'            => getenv('APP_ENV'),
        ];
    }
}
