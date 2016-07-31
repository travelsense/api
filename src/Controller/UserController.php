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
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Validator;

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
     * @var Validator
     */
    private $validator;

    /**
     * @var string
     */
    private $user_json_schema_for_registration;

    /**
     * UserController constructor.
     *
     * @param UserMapper       $user_mapper
     * @param MailerService    $mailer
     * @param ExpirableStorage $storage
     * @param Validator        $validator
     * @param                  $user_json_schema_for_registration
     */
    public function __construct(
        UserMapper $user_mapper,
        MailerService $mailer,
        ExpirableStorage $storage,
        Validator $validator,
        $user_json_schema_for_registration
    ) {
        $this->user_mapper = $user_mapper;
        $this->mailer = $mailer;
        $this->storage = $storage;
        $this->validator = $validator;
        $this->user_json_schema_for_registration = $user_json_schema_for_registration;
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
     * Validation of user json-data
     *
     * @param string $json
     * @return bool
     * @throws ApiException
     */
    public function isValidUser(string $json): bool
    {
        $refResolver = new RefResolver(new UriRetriever(), new UriResolver());
        $data = json_decode($json);
        $schema = $refResolver->resolve('file://'. realpath(__DIR__ . $this->user_json_schema_for_registration));
        $this->validator->check($data, $schema);
        if($this->validator->isValid())
            return true;
        else
            throw new ApiException(
                'Received json-data does not correspond to the above json-schema',
                ApiException::VALIDATION
            );
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
        $this->isValidUser($request->getContent());
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
            ->setCreator($json->has('creator') ? $json->getBoolean('creator') : false);
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
