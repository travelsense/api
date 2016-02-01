<?php

namespace Controller;

use ExpirableStorage;
use Mapper\DB\UserMapper;
use Model\User;
use Psr\Log\LoggerAwareTrait;
use Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User Web controller
 */
class UserController
{
    use LoggerAwareTrait;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var MailerService
     */
    private $mailer;

    /**
     * @var ExpirableStorage
     */
    private $storage;

    /**
     * UserController constructor.
     * @param UserMapper $userMapper
     * @param MailerService $mailer
     * @param ExpirableStorage $storage
     */
    public function __construct(
        UserMapper $userMapper,
        MailerService $mailer,
        ExpirableStorage $storage
    )
    {
        $this->userMapper = $userMapper;
        $this->mailer = $mailer;
        $this->storage = $storage;
    }

    /**
     * @param string $token
     * @return Response
     */
    public function confirmEmail($token)
    {
        /** @var User $user */
        $email = $this->storage->get($token);
        if ($email === null) {
            if ($this->logger) {
                $this->logger->warning('Expired token', ['token' => $token]);
            }
            return new Response('Invalid token.');
        }
        if ($this->userMapper->emailExists($email)) {
            $this->userMapper->confirmEmail($email);
        } else {
            if ($this->logger) {
                $this->logger->error('Email not found', ['token' => $token, 'email' => $email]);
            }
        }
        return new Response('Account has been created.');
    }



    public function showPasswordChangeForm(Request $request)
    {
        return 'hello';
    }
}