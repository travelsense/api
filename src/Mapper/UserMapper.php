<?php

namespace Mapper;

class UserMapper extends AbstractMapper
{
    /**
     * @var string
     */
    private $salt;

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function hasEmail($email)
    {
        $select = $this->pdo->prepare('SELECT id FROM users WHERE email=:email');
        $select->execute([':email' => $email]);
        return $select->rowCount() == 1;
    }

    /**
     * Insert a new user
     * @param array $user
     */
    public function createUser(array $user)
    {
        $insert = $this->pdo->prepare('INSERT INTO users ("email", "password") VALUES (:email, :password)');
        $insert->execute($user);
    }
}