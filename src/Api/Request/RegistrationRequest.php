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
     * @var string
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @Assert\Length(min = 8)
     */
    public $password;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $firstName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $lastName;

    /**
     * @var string
     */
    public $picture;
}