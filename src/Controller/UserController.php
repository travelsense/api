<?php

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


    /**
     * Create a new user
     * @param array $payload
     * @throws ApiException
     */
    public function create(array $payload)
    {
        $email = A::get($payload, 'email');
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $password = A::get($payload, 'password');

        if ( ! $email) {
            throw ApiException::create(ApiException::INVALID_EMAIL);
        }

        if (mb_strlen($password)) {
            throw ApiException::create(ApiException::PASSWORD_TOO_SHORT);
        }

        if ($this->userMapper->hasEmail($email)) {
            throw ApiException::create(ApiException::USER_EXISTS);
        }

    }
}