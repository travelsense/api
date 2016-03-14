<?php

namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\User;

class UserMapper extends AbstractPDOMapper
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
    public function emailExists($email)
    {
        $select = $this->prepare('SELECT id FROM users WHERE email=:email');
        $select->execute([':email' => $email]);
        return $select->rowCount() == 1;
    }

    /**
     * @param string $email
     * @return void
     */
    public function confirmEmail($email)
    {
        $select = $this->prepare('UPDATE users SET email_confirmed = true WHERE email=:email');
        $select->execute([':email' => $email]);
    }

    /**
     * Insert into DB, update id
     *
     * @param User $user
     */
    public function insert(User $user)
    {
        $sql = <<<SQL
INSERT INTO users
  ("email", "password", "first_name", "last_name", "picture")
VALUES
  (:email, :password, :first_name, :last_name, :picture)
RETURNING id
SQL;
        $insert = $this->prepare($sql);
        $insert->execute(
            [
            ':email' => $user->getEmail(),
            ':password' => $this->getPasswordHash($user->getPassword()),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':picture' => $user->getPicture(),
            ]
        );
        $id = $insert->fetchColumn();
        $user->setId($id);
    }

    /**
     * @param $email
     * @param $password
     * @return User|null
     */
    public function fetchByEmailAndPassword($email, $password)
    {
        $select = $this->prepare('SELECT * FROM users WHERE email = :email AND "password" = :password');

        $select->execute(
            [
            ':email' => $email,
            ':password' => $this->getPasswordHash($password),
            ]
        );
        return $this->fetch($select);
    }

    /**
     * @param $id
     * @return User|null
     */
    public function fetchById($id)
    {
        $select = $this->prepare('SELECT * FROM users WHERE "id" = :id');
        $select->execute(
            [
            ':id' => $id,
            ]
        );
        return $this->fetch($select);
    }

    /**
     * @param $email
     * @return User|null
     */
    public function fetchByEmail($email)
    {
        $select = $this->prepare('SELECT * FROM users WHERE "email" = :email');
        $select->execute(
            [
            ':email' => $email,
            ]
        );
        return $this->fetch($select);
    }

    /**
     * @param $password
     * @return string
     */
    private function getPasswordHash($password)
    {
        return sha1($password . $this->salt);
    }

    /**
     * @param $email
     * @param $password
     * @return User|null
     */
    public function updatePasswordByEmail($email, $password)
    {
        $update = $this->pdo->prepare('UPDATE users SET password= :password WHERE email= :email');
        return $update->execute(
            [
                ':email' => $email,
                ':password' => $this->getPasswordHash($password)
            ]
        );
    }

    /**
     * @param array $row
     * @return User
     */
    protected function create(array $row)
    {
        $user = new User();
        return $user
            ->setId($row['id'])
            ->setEmail($row['email'])
            ->setFirstName($row['first_name'])
            ->setLastName($row['last_name'])
            ->setPicture($row['picture']);
    }
}
