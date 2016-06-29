<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\Mapper\DB\IATAMapper;

class IataController extends ApiController
{
    /**
     * @var IATAMapper
     */
    private $iata_mapper;

    /**
     * IataController constructor.
     * @param IATAMapper $iata_mapper
     */
    public function __construct(IATAMapper $iata_mapper)
    {
        $this->iata_mapper = $iata_mapper;
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     * @throws ApiException
     */
    public function getOne(string $type, string $code): array
    {
        $object = $this->iata_mapper->fetchOne($type, $code);
        if ($object) {
            return $object;
        } else {
            throw new ApiException('Not found', ApiException::RESOURCE_NOT_FOUND);
        }
    }

    /**
     * @param string $type
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function getAll(string $type, int $limit = 10, int $offset = 0): array
    {
        return $this->iata_mapper->fetchAll($type, $limit, $offset);
    }
}
