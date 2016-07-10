<?php

namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\User;
use DateTime;
use PDO;

class UserMapper extends AbstractPDOMapper
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
        $select = $this->pdo->prepare('SELECT id FROM users WHERE email=:email');
        $select->execute([':email' => $email]);
        return $select->rowCount() == 1;
    }

    /**
     * @param string $email
     * @return void
     */
    public function confirmEmail(string $email)
    {
        $select = $this->pdo->prepare('UPDATE users SET email_confirmed = true WHERE email=:email');
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
  ("email", "password", "first_name", "last_name", "picture", "creator")
VALUES
  (:email, :password, :first_name, :last_name, :picture, :creator)
RETURNING id, created
SQL;
        $insert = $this->pdo->prepare($sql);
		$insert->bindValue('email', $user->getEmail(), PDO::PARAM_STR);
		$insert->bindValue('password', $this->getPasswordHash($user->getPassword()), PDO::PARAM_STR);
		$insert->bindValue('first_name', $user->getFirstName(), PDO::PARAM_STR);
		$insert->bindValue('last_name', $user->getLastName(), PDO::PARAM_STR);
		$insert->bindValue('picture', $user->getPicture(), PDO::PARAM_STR);
		$insert->bindValue('creator', $user->getCreator(), PDO::PARAM_BOOL);
       
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
        $id = $user->getId();
        $creator = $user->getCreator();
        $update = $this->pdo->prepare('UPDATE users SET email = :email, first_name = :firstname, last_name = :lastname, email_confirmed = :email_confirmed WHERE id = :id');
        $update->bindValue(':email', $email);
        $update->bindValue(':firstname', $first_name);
        $update->bindValue(':lastname', $last_name);
        $update->bindValue(':email_confirmed', $email_confirmed, PDO::PARAM_BOOL);
        $update->bindValue(':id', $id);
        $update->bindValue(':creator', $creator, PDO::PARAM_BOOL);
        $update->execute();
    }

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function fetchByEmailAndPassword(string $email, string $password)
    {
        $select = $this->pdo->prepare('SELECT * FROM users WHERE email = :email AND "password" = :password');

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
        $select = $this->pdo->prepare('SELECT * FROM users WHERE "id" = :id');
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
        $select = $this->pdo->prepare('SELECT * FROM users WHERE "email" = :email');
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
        $update = $this->pdo->prepare('UPDATE users SET password= :password WHERE email= :email');
        return $update->execute(
            [
                ':email'    => $email,
                ':password' => $this->getPasswordHash($password),
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
            ->setCreated(new DateTime($row['created']))
            ->setEmailConfirmed($row['email_confirmed'])
            ->setCreator($row['creator']);
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
