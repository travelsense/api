<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\ExpirableStorage;
use Api\JSON\DataObject;
use Api\Mapper\DB\UserMapper;
use Api\Model\User;
use Api\Service\Mailer;
use Symfony\Component\HttpFoundation\Request;

/**
 * User API controller
 */
class UserController extends ApiController
{
    /**
     * @var UserMapper
     */
    private $user_mapper;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var ExpirableStorage
     */
    private $storage;

    /**
     * UserController constructor.
     *
     * @param UserMapper       $user_mapper
     * @param Mailer           $mailer
     * @param ExpirableStorage $storage
     */
    public function __construct(
        UserMapper $user_mapper,
        Mailer $mailer,
        ExpirableStorage $storage
    ) {
        $this->user_mapper = $user_mapper;
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
            ->setPicture($json->has('picture') ? $json->getString('picture') : '')
            ->setCreator($json->has('creator') ? $json->getBoolean('creator') : false);

        if ($this->user_mapper->emailExists($user->getEmail())) {
            throw new ApiException('Email already exists', ApiException::USER_EXISTS);
        }
        $this->user_mapper->insert($user);
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
        if (false === $this->user_mapper->emailExists($email)) {
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
        if (false === $this->user_mapper->emailExists($email)) {
            if ($this->logger) {
                $this->logger->error('Email not found', ['token' => $token, 'email' => $email]);
            }
            throw new ApiException('Email not found', ApiException::RESOURCE_NOT_FOUND);
        }
        $this->user_mapper->confirmEmail($email);
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
        if (false === $this->user_mapper->emailExists($email)) {
            if ($this->logger) {
                $this->logger->error('Email not found', ['token' => $token, 'email' => $email]);
            }
            throw new ApiException('Email not found', ApiException::RESOURCE_NOT_FOUND);
        }
        $json = DataObject::createFromString($request->getContent());
        $password = $json->getString('password');
        $this->user_mapper->updatePasswordByEmail($email, $password);
        return [];
    }
}
