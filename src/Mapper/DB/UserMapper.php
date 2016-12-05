<?php

namespace Api\Mapper\DB;

use Api\DB\AbstractMapper;
use Api\Model\User;
use DateTime;
use PDO;

class UserMapper extends AbstractMapper
{
    /**
     * @var string
     */
    private $salt;

    /**
     * @param string $salt
     */
    public function setSalt(string $salt)
    {
        $this->salt = $salt;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email)
    {
        $select = $this->connection->prepare('SELECT id FROM users WHERE email=:email');
        $select->execute([':email' => $email]);
        return $select->rowCount() == 1;
    }

    /**
     * @param string $email
     * @return void
     */
    public function confirmEmail(string $email)
    {
        $select = $this->connection->prepare('UPDATE users SET email_confirmed = true WHERE email=:email');
        $select->execute([':email' => $email]);
    }

    /**
     * Insert into DB, update id
     *
     * @param User $user
     */
    public function insert(User $user)
    {
        $insert = $this->connection->prepare(
            'INSERT INTO users
              ("email", "password", "first_name", "last_name", "picture", "creator")
            VALUES
              (:email, :password, :first_name, :last_name, :picture, :creator)
            RETURNING id, created'
        );
        $values = [
            ':email'      => $user->getEmail(),
            ':password'   => $this->getPasswordHash($user->getPassword()),
            ':first_name' => $user->getFirstName(),
            ':last_name'  => $user->getLastName(),
            ':picture'    => $user->getPicture(),
            ':creator'    => $user->isCreator(),
        ];
        $this->helper->bindValues($insert, $values);
        $insert->execute();
        $row = $insert->fetch(PDO::FETCH_ASSOC);
        $user->setId($row['id']);
        $user->setCreated(new DateTime($row['created']));
    }

    /**
     * @param User $user
     * @return void
     */
    public function update(User $user)
    {
        $email = $user->getEmail();
        $first_name = $user->getFirstName();
        $last_name = $user->getLastName();
        $email_confirmed = $user->isEmailConfirmed();
        $creator = $user->isCreator();
        $id = $user->getId();
        $update = $this->connection->prepare(
            'UPDATE users 
            SET 
              email = :email, 
              first_name = :firstname, 
              last_name = :lastname, 
              email_confirmed = :email_confirmed, 
              creator = :creator 
            WHERE id = :id'
        );
        $values = [
            'email' => $email,
            'firstname' => $first_name,
            'lastname' => $last_name,
            'email_confirmed' => $email_confirmed,
            'id' => $id,
            'creator' => $creator,
        ];
        $this->helper->bindValues($update, $values);
        $update->execute();
    }

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function fetchByEmailAndPassword(string $email, string $password)
    {
        $select = $this->connection->prepare('SELECT * FROM users WHERE email = :email AND "password" = :password');

        $select->execute(
            [
                ':email'    => $email,
                ':password' => $this->getPasswordHash($password),
            ]
        );
        $row = $select->fetch(PDO::FETCH_NAMED);
        return $row ? $this->create($row) : null;
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function fetchById(int $id)
    {
        $select = $this->connection->prepare('SELECT * FROM users WHERE "id" = :id');
        $select->execute(
            [
                ':id' => $id,
            ]
        );
        $row = $select->fetch(PDO::FETCH_NAMED);
        return $row ? $this->create($row) : null;
    }

    /**
     * @param $email
     * @return User|null
     */
    public function fetchByEmail(string $email)
    {
        $select = $this->connection->prepare('SELECT * FROM users WHERE "email" = :email');
        $select->execute(
            [
                ':email' => $email,
            ]
        );
        $row = $select->fetch(PDO::FETCH_NAMED);
        return $row ? $this->create($row) : null;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function updatePasswordByEmail(string $email, string $password)
    {
        $update = $this->connection->prepare('UPDATE users SET password= :password WHERE email= :email');
        return $update->execute(
            [
                ':email'    => $email,
                ':password' => $this->getPasswordHash($password),
            ]
        );
    }

    /**
     * @param int $user_id
     * @param string $new_pic
     * @return bool
     */
    public function updatePic(int $user_id, string $new_pic)
    {
        $update = $this->connection->prepare('UPDATE users SET "picture" = :picture WHERE "id" = :id');
        return $update->execute(
            [
                ':picture' => $new_pic,
                ':id' => $user_id,
            ]
        );
    }

    /**
     * @param array $row
     * @return User
     */
    protected function create(array $row): User
    {
        $user = new User();
        return $user
            ->setId($row['id'])
            ->setEmail($row['email'])
            ->setFirstName($row['first_name'])
            ->setLastName($row['last_name'])
            ->setPicture($row['picture'])
            ->setCreator($row['creator'])
            ->setCreated(new DateTime($row['created']))
            ->setEmailConfirmed($row['email_confirmed']);
    }

    /**
     * @param string $password
     * @return string
     */
    private function getPasswordHash(string $password): string
    {
        return sha1($password . $this->salt);
    }
}
