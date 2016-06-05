<?php
namespace Api\Controller;

use Api\Migrator\Migrator;
use Symfony\Component\HttpFoundation\Request;

class HealthCheckController extends ApiController
{
    /**
     * @var Migrator
     */
    private $migrator;

    /**
     * HealthCheckController constructor.
     * @param Migrator $migrator
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function healthCheck(Request $request): array
    {
        return [
            'env'        => getenv('APP_ENV'),
            'db_version' => $this->migrator->getVersion(),
        ];
    }
}
