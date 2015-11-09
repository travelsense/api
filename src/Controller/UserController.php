<?php

namespace Controller;

use A;
use Exception\ApiException;
use Mapper\UserMapper;
use Security\Authentication\Credentials;
use Security\SessionManager;
use Security\TokenManager;
use Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Request;
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
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * UserController constructor.
     * @param UserMapper $userMapper
     * @param MailerService $mailer
     * @param TokenManager $secureToken
     * @param SessionManager $sessionManager
     */
    public function __construct(UserMapper $userMapper, MailerService $mailer, TokenManager $secureToken, SessionManager $sessionManager, Credentials $credentials)
    {
        $this->userMapper = $userMapper;
        $this->mailer = $mailer;
        $this->tokenManager = $secureToken;
        $this->sessionManager = $sessionManager;
        $this->credentials = $credentials;
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

        $token = $this->tokenManager->encrypt($payload);
        $this->mailer->sendAccountConfirmationMessage($email, $token);
        return [];
    }

    /**
     * @param $token
     * @return Response
     */
    public function finishRegisterThroughEmail($token)
    {
        $payload = $this->tokenManager->decrypt($token);
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

    /**
     * @param array $payload
     * @param Request $request
     * @return array
     * @throws ApiException
     */
    public function loginByEmailAndPassword(array $payload, Request $request)
    {
        $email = A::get($payload, 'email');
        $password = A::get($payload, 'password');
        $user = $this->userMapper->fetchByEmailAndPassword($email, $password);
        if (null === $user) {
            throw ApiException::create(ApiException::INVALID_EMAIL_PASSWORD);
        }
        $device = $request->headers->get('User-Agent');
        $token = $this->sessionManager->createSession($user['id'], $device);
        return [
            'token' => $token,
        ];
    }

    /**
     * @return array
     */
    public function getUser()
    {
        $userId = $this->credentials->getUser();
        $user = $this->userMapper->fetchById($userId);
        return [
            'email' => $user['email'],
        ];
    }
}