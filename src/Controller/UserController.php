<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\ExpirableStorage;
use Api\JSON\DataObject;
use Api\Mapper\DB\UserMapper;
use Api\Model\User;
use Api\Service\Mailer\MailerService;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * User API controller
 */
class UserController extends ApiController
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
     * @var ExpirableStorage
     */
    private $storage;

    /**
     * UserController constructor.
     *
     * @param UserMapper       $userMapper
     * @param MailerService    $mailer
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
            'id'        => $user->getId(),
            'email'     => $user->getEmail(),
            'picture'   => $user->getPicture(),
            'firstName' => $user->getFirstName(),
            'lastName'  => $user->getLastName(),
            'created'   => $user->getCreated()->format(self::DATETIME_FORMAT),
        ];
    }

    /**
     * Send account confirmation link
     * @param User $user
     */
    public function sendConfirmationLink(User $user)
    {
        $token = $this->storage->store($user->getEmail());
        if ($this->logger) {
            $this->logger->info('Expirable token created', ['token' => $token]);
        }
        $this->mailer->sendAccountConfirmationMessage($user->getEmail(), $token);
    }

    /**
     * Start email-based registration. Send a confirmation email.
     *
     * @param  Request $request
     * @return array
     * @throws ApiException
     */
    public function createUser(Request $request): array
    {
        $json = DataObject::createFromString($request->getContent());

        $user = new User();
        $user
            ->setEmail($json->getString('email'))
            ->setPassword($json->getString('password'))
            ->setFirstName($json->getString('firstName'))
            ->setLastName($json->getString('lastName'))
            ->setPicture($json->has('picture') ? $json->getString('picture') : '');

        if ($this->userMapper->emailExists($user->getEmail())) {
            throw new ApiException('Email already exists', ApiException::USER_EXISTS);
        }
        $this->userMapper->insert($user);
        $this->sendConfirmationLink($user);
        return [];
    }

    /**
     * Send password reset link
     *
     * @param  string $email
     * @return array
     * @throws ApiException
     */
    public function sendPasswordResetLink(string $email): array
    {
        if (false === $this->userMapper->emailExists($email)) {
            throw new ApiException('Email not found', ApiException::RESOURCE_NOT_FOUND);
        }
        $token = $this->storage->store($email);
        $this->mailer->sendPasswordResetLink($email, $token);
        return [];
    }

    /**
     * @param string $token
     * @return array
     * @throws ApiException
     */
    public function confirmEmail(string $token): array
    {
        /* @var User $user */
        $email = $this->storage->get($token);
        if ($email === null) {
            if ($this->logger) {
                $this->logger->warning('Expired token', ['token' => $token]);
            }
            throw new ApiException('Token expired', ApiException::RESOURCE_NOT_FOUND);
        }
        if (false === $this->userMapper->emailExists($email)) {
            if ($this->logger) {
                $this->logger->error('Email not found', ['token' => $token, 'email' => $email]);
            }
            throw new ApiException('Email not found', ApiException::RESOURCE_NOT_FOUND);
        }
        $this->userMapper->confirmEmail($email);
        return [];
    }

    /**
     * @param string  $token
     * @param Request $request
     * @return array
     * @throws ApiException
     */
    public function resetPassword(string $token, Request $request): array
    {
        /* @var User $user */
        $email = $this->storage->get($token);
        if ($email === null) {
            if ($this->logger) {
                $this->logger->warning('Expired token', ['token' => $token]);
            }
            throw new ApiException('Token expired', ApiException::RESOURCE_NOT_FOUND);
        }
        if (false === $this->userMapper->emailExists($email)) {
            if ($this->logger) {
                $this->logger->error('Email not found', ['token' => $token, 'email' => $email]);
            }
            throw new ApiException('Email not found', ApiException::RESOURCE_NOT_FOUND);
        }
        $json = DataObject::createFromString($request->getContent());
        $password = $json->getString('password');
        $this->userMapper->updatePasswordByEmail($email, $password);
        return [];
    }

    /**
     * Update User data by id
     *
     * @param User    $user
     * @param Request $request
     * @return array
     */
    public function updateUser(User $user, Request $request): array
    {
        $json = DataObject::createFromString($request->getContent());
        $email = $json->getString('email');
        $emailUpdate = ($user->getEmail() !== $email);
        $user
            ->setEmail($json->getString('email'))
            ->setFirstName($json->getString('firstName'))
            ->setLastName($json->getString('lastName'));
        if ($emailUpdate) {
            $user->setEmailConfirmed(false);
        }
        $this->userMapper->update($user);
        if ($emailUpdate) {
            $this->sendConfirmationLink($user);
        }
        return [];
    }
}
