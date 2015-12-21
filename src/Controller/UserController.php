<?php

namespace Controller;

use Exception\ApiException;
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use ExpirableStorage;
use Mapper\DB\UserMapper as DBUserMapper;
use Mapper\JSON\UserMapper as JSONUserMapper;
use Security\Authentication\Credentials;
use Security\SessionManager;
use Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Model\User;

class UserController
{
    /**
     * @var DBUserMapper
     */
    private $dbUserMapper;

    /**
     * @var JSONUserMapper
     */
    private $jsonUserMapper;

    /**
     * @var MailerService
     */
    private $mailer;

    /**
     * @var ExpirableStorage
     */
    private $storage;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var PasswordGeneratorInterface
     */
    private $pwdGenerator;

    /**
     * UserController constructor.
     * @param DBUserMapper $dbUserMapper
     * @param JSONUserMapper $jsonUserMapper
     * @param MailerService $mailer
     * @param ExpirableStorage $storage
     * @param SessionManager $sessionManager
     * @param Credentials $credentials
     * @param Facebook $facebook
     * @param PasswordGeneratorInterface $pwdGenerator
     */
    public function __construct(
        DBUserMapper $dbUserMapper,
        JSONUserMapper $jsonUserMapper,
        MailerService $mailer,
        ExpirableStorage $storage,
        SessionManager $sessionManager,
        Credentials $credentials,
        Facebook $facebook,
        PasswordGeneratorInterface $pwdGenerator
    )
    {
        $this->dbUserMapper = $dbUserMapper;
        $this->jsonUserMapper = $jsonUserMapper;
        $this->mailer = $mailer;
        $this->storage = $storage;
        $this->sessionManager = $sessionManager;
        $this->credentials = $credentials;
        $this->facebook = $facebook;
        $this->pwdGenerator = $pwdGenerator;
    }

    /**
     * @return array
     */
    public function getUser()
    {
        $userId = $this->credentials->getUser();
        $user = $this->dbUserMapper->fetchById($userId);
        return $this->jsonUserMapper->toArray($user);
    }

    /**
     * Start email-based registration. Send a confirmation email.
     * @param Request $request
     * @return array
     * @throws ApiException
     */
    public function createUser(Request $request)
    {
        $user = $this->jsonUserMapper->createUser($request);
        if ($this->dbUserMapper->emailExists($user->getEmail())) {
            throw ApiException::create(ApiException::USER_EXISTS);
        }
        $this->dbUserMapper->insert($user);
        $token = $this->storage->store($user->getEmail());
        $this->mailer->sendAccountConfirmationMessage($user->getEmail(), $token);
        return [];
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
            return new Response('Invalid token.');
        }
        if (false === $this->dbUserMapper->emailExists($email)) {
            $this->dbUserMapper->confirmEmail($email);
        }
        return new Response('Account has been created.');
    }

    /**
     * @param string $email
     * @param Request $request
     * @return array
     * @throws ApiException
     */
    public function createTokenByEmail($email, Request $request)
    {
        $password = $request->getContent();
        $user = $this->dbUserMapper->fetchByEmailAndPassword($email, $password);
        if (null === $user) {
            throw ApiException::create(ApiException::INVALID_EMAIL_PASSWORD);
        }
        $token = $this->sessionManager->createSession($user->getId(), $request);
        return new JsonResponse($token);
    }


    /**
     * @param $fbToken
     * @param Request $request
     * @return array
     */
    public function createTokenByFacebook($fbToken, Request $request)
    {
        $this->facebook->setDefaultAccessToken($fbToken);
        $fbUser = $this->facebook
            ->get('/me?fields=picture,email,first_name,last_name')
            ->getGraphUser();
        $user = $this->dbUserMapper->fetchByEmail($fbUser->getEmail());
        if (null === $user) {
            $pic = $fbUser->getPicture();
            $user = new User();
            $user
                ->setEmail($fbUser->getEmail())
                ->setFirstName($fbUser->getFirstName())
                ->setLastName($fbUser->getLastName())
                ->setPicture($pic ? $pic->getUrl() : null)
                ->setPassword($this->pwdGenerator->generatePassword());
            $this->dbUserMapper->insert($user);
        }
        $token = $this->sessionManager->createSession($user->getId(), $request);
        return new JsonResponse($token);
    }

    public function showPasswordChangeForm(Request $request)
    {
        return 'hello';
    }
}