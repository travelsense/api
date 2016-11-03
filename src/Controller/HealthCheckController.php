<?php
namespace Api\Controller;

class HealthCheckController extends ApiController
{
    /**
     * @return array
     */
    public function healthCheck(): array
    {
        return [
            'env' => getenv('APP_ENV'),
        ];
    }
}
