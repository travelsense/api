<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 10/31/15
 * Time: 5:32 PM
 */

namespace Mapper;

use PDO;

class AbstractMapper
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * AbstractMapper constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}