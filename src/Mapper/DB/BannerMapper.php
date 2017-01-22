<?php
namespace Api\Mapper\DB;

use Api\DB\AbstractMapper;

class BannerMapper extends AbstractMapper
{
    /**
     * @return array[]
     */
    public function fetchBanners(): array
    {
        $select = $this->connection->prepare('SELECT title, subtitle, image, category FROM banners');
        $select->execute();

        return $select->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function create(array $row)
    {
        throw new \BadMethodCallException();
    }
}
