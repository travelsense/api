<?php

namespace Mapper\DB;
use PDO;
use PDOStatement;
use User;

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
    public function emailExists($email)
    {
        $select = $this->pdo->prepare('SELECT id FROM users WHERE email=:email');
        $select->execute([':email' => $email]);
        return $select->rowCount() == 1;
    }

    /**
     * @param string $email
     * @return void
     */
    public function confirmEmail($email)
    {
        $select = $this->pdo->prepare('UPDATE users SET email_confirmed = true WHERE email=:email');
        $select->execute([':email' => $email]);
    }



    /**
     * Insert a new user
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
        $insert = $this->pdo->prepare($sql);
        $insert->execute([
            ':email' => $user->getEmail(),
            ':password' => $this->getPasswordHash($user->getPassword()),
            ':first_name' => $user->getFirstName(),
            ':last_name' => $user->getLastName(),
            ':picture' => $user->getPicture(),
        ]);
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
        $select = $this->pdo->prepare('SELECT * FROM users WHERE email = :email AND "password" = :password');

        $select->execute([
            ':email' => $email,
            ':password' => $this->getPasswordHash($password),
        ]);
        return $this->createUser($select);
    }

    /**
     * @param $id
     * @return User|null
     */
    public function fetchById($id)
    {
        $select = $this->pdo->prepare('SELECT * FROM users WHERE "id" = :id');
        $select->execute([
            ':id' => $id,
        ]);
        return $this->createUser($select);
    }

    /**
     * @param $email
     * @return User|null
     */
    public function fetchByEmail($email)
    {
        $select = $this->pdo->prepare('SELECT * FROM users WHERE "email" = :email');
        $select->execute([
            ':email' => $email,
        ]);
        return $this->createUser($select);
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
     * @param PDOStatement $stmt
     * @return User|null
     */
    private function createUser(PDOStatement $stmt)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (false === $row) {
            return null;
        }
        $user = new User();
        return $user
            ->setId($row['id'])
            ->setEmail($row['email'])
            ->setFirstName($row['first_name'])
            ->setLastName($row['last_name'])
            ->setPicture($row['picture']);
    }
}