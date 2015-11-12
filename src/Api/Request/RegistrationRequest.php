<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/11/15
 * Time: 6:10 PM
 */

namespace Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationRequest extends Request
{
    /**
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\Length(min = 8)
     */
    public $password;

    public $firstName;

    public $lastName;

    public $picture;
}