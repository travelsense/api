<?php

namespace Controller\Api;

use Exception\ApiException;
use ExpirableStorage;
use JSON\DataObject;
use JSON\FormatException;
use Mapper\DB\UserMapper;
use Model\User;
use Psr\Log\LoggerAwareTrait;
use Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User API controller
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
     * @param User $user
     * @return array
     */
    public function getUser(User $user)
    {
        return [
            'email' => $user->getEmail(),
            'picture' => $user->getPicture(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ];
    }

    /**
     * Start email-based registration. Send a confirmation email.
     * @param Request $request
     * @return array
     * @throws ApiException
     */
    public function createUser(Request $request)
    {
        $json = new DataObject($request->getContent());

        $user = new User();
        try {
            $user
                ->setEmail($json->getString('email'))
                ->setPassword($json->getString('password'))
                ->setFirstName($json->getString('firstName'))
                ->setLastName($json->getString('lastName'))
                ->setPicture($json->has('picture') ? $json->getString('picture') : '');
        } catch (FormatException $e) {
            throw new ApiException($e->getMessage(), ApiException::VALIDATION, $e, Response::HTTP_BAD_REQUEST);
        }

        if ($this->userMapper->emailExists($user->getEmail())) {
            throw ApiException::create(ApiException::USER_EXISTS);
        }
        $this->userMapper->insert($user);
        $token = $this->storage->store($user->getEmail());
        if ($this->logger) {
            $this->logger->info('Expirable token created', ['token' => $token]);
        }
        $this->mailer->sendAccountConfirmationMessage($user->getEmail(), $token);
        return [];
    }
}