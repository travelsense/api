<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\Mapper\DB\IATAMapper;
use Symfony\Component\HttpFoundation\Request;

class IataController extends ApiController
{
    /**
     * @var IATAMapper
     */
    private $iataMapper;

    /**
     * IataController constructor.
     * @param IATAMapper $iataMapper
     */
    public function __construct(IATAMapper $iataMapper)
    {
        $this->iataMapper = $iataMapper;
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     * @throws ApiException
     */
    public function getOne(string $type, string $code): array
    {
        $object = $this->iataMapper->fetchOne($type, $code);
        if ($object) {
            return $object;
        } else {
            throw new ApiException('Not found', ApiException::RESOURCE_NOT_FOUND);
        }
    }

    /**
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll(string $type, int $limit = 10, int $offset = 0): array
    {
        return $this->iataMapper->fetchAll($type, $limit, $offset);
    }
}
