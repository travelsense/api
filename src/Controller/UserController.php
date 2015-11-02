<?php

namespace Controller;

use A;
use Exception\ApiException;
use Mapper\UserMapper;
use SecureToken;
use Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var MailerService
     */
    private $mailer;

    /**
     * @var SecureToken
     */
    private $secureToken;

    /**
     * UserController constructor.
     * @param UserMapper $userMapper
     * @param MailerService $mailer
     * @param SecureToken $secureToken
     */
    public function __construct(UserMapper $userMapper, MailerService $mailer, SecureToken $secureToken)
    {
        $this->userMapper = $userMapper;
        $this->mailer = $mailer;
        $this->secureToken = $secureToken;
    }

    /**
     * Start email-based registration. Send a confirmation email.
     * @param array $payload
     * @return array
     * @throws ApiException
     */
    public function startRegisterThroughEmail(array $payload)
    {
        $email = A::get($payload, 'email');
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $password = A::get($payload, 'password');

        if ( ! $email) {
            throw ApiException::create(ApiException::INVALID_EMAIL);
        }

        if (mb_strlen($password) < 8) {
            throw ApiException::create(ApiException::PASSWORD_TOO_SHORT);
        }

        if ($this->userMapper->hasEmail($email)) {
            throw ApiException::create(ApiException::USER_EXISTS);
        }

        $token = $this->secureToken->encrypt($payload);
        $this->mailer->sendAccountConfirmationMessage($email, $token);
        return [];
    }

    /**
     * @param $token
     * @return Response
     */
    public function finishRegisterThroughEmail($token)
    {
        $payload = $this->secureToken->decrypt($token);
        if ($payload === null) {
            return new Response('Invalid token.');
        }
        $email = A::get($payload, 'email');
        if ( ! $this->userMapper->hasEmail($email))
        {
            $this->userMapper->createUser($payload);
        }
        return new Response('Account has been created.');
    }
}