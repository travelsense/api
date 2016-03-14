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
     * @param $type
     * @param string $code
     * @return false|object
     * @throws ApiException
     */
    public function getOne($type, $code)
    {
        $object = $this->iataMapper->fetchOne($type, $code);
        if ($object) {
            return $object;
        } else {
            throw ApiException::create(ApiException::RESOURCE_NOT_FOUND);
        }
    }

    /**
     * @param $type
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($type, $limit = 10, $offset = 0)
    {
        return $this->iataMapper->fetchAll($type, $limit, $offset);
    }
}
