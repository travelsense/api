<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 10/31/15
 * Time: 2:57 PM
 */

namespace Controller;

use Exception\ApiException;
use Mapper\UserMapper;
use A;

class UserController
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * User constructor.
     * @param UserMapper $userMapper
     */
    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }


    public function create($payload)
    {
        $email = A::get($payload, 'email');
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $password = A::get($payload, 'password');

        if ( ! $email) {
            throw new ApiException('Invalid email');
        }

        if (mb_strlen($password)) {
            throw new ApiException('Invalid email');
        }

        if ($this->userMapper->hasEmail($email))

    }
}