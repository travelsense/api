<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 10/31/15
 * Time: 2:57 PM
 */

namespace Controller;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Token;
use PDO;
use A;

class User
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($payload)
    {
        $email = A::get($payload, 'email');

    }
}