<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Banner;
use PDO;

class BannerMapper extends AbstractPDOMapper
{
    /**
     * @return array[]
     */
    public function fetchBanners() : array
    {
        $select = $this->pdo->prepare('SELECT title, subtitle, image, category FROM banners');
        $select->execute();

        return $select->fetchAll(PDO::FETCH_NAMED);
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
