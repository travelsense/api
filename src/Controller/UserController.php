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
    private $user_mapper;

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
     * @param UserMapper       $user_mapper
     * @param MailerService    $mailer
     * @param ExpirableStorage $storage
     */
    public function __construct(
        UserMapper $user_mapper,
        MailerService $mailer,
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
            'creator'   => $user->isCreator(),
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
            ->setCreator($json->has('picture') ? $json->get('creator') : '');

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
        $email_update = ($user->getEmail() !== $email);
        $user
            ->setEmail($json->getString('email'))
            ->setFirstName($json->getString('firstName'))
            ->setLastName($json->getString('lastName'))
            ->setCreator($json->get('creator'));
        if ($email_update) {
            $user->setEmailConfirmed(false);
        }
        $this->user_mapper->update($user);
        if ($email_update) {
            $this->sendConfirmationLink($user);
        }
        return [];
    }
}
