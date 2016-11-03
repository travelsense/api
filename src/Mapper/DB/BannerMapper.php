<?php
namespace Api\Mapper\DB;

use Api\DB\AbstractMapper;
use Api\Model\Travel\Banner;
use PDO;

class BannerMapper extends AbstractMapper
{
    /**
     * @return array[]
     */
    public function fetchBanners(): array
    {
        $select = $this->connection->prepare('SELECT title, subtitle, image, category FROM banners');
        $select->execute();

        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array $row
     * @return Banner
     */
    protected function create(array $row): Banner
    {
        $banner = new Banner();
        return $banner
            ->setId($row['id'])
            ->setTitle($row['title'])
            ->setSubtitle($row['subtitle'])
            ->setImage($row['image'])
            ->setCategory($row['category']);
    }
}
