<?php
namespace Api\Model;

class User
{
    use IdTrait;
    use TimestampTrait;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $email_confirmed = false;

    /**
     * @var string
     */
    private $first_name;

    /**
     * @var string
     */
    private $last_name;

    /**
     * @var string
     */
    private $picture;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $creator = false;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     *
     * @return bool
     */
    public function isEmailConfirmed()
    {
        return $this->email_confirmed;
    }

    /**
     *
     * @param bool $email_confirmed
     * @return User
     */
    public function setEmailConfirmed(bool $email_confirmed)
    {
        $this->email_confirmed = $email_confirmed;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     * @return User
     */
    public function setFirstName(string $first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     * @return User
     */
    public function setLastName(string $last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     * @return User
     */
    public function setPicture(string $picture = null)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param bool $creator
     * @return User
     */
    public function setCreator(bool $creator)
    {
        $this->creator = $creator;
        return $this;
    }
}
