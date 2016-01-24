<?php

namespace Controller;

use Exception\ApiException;
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use ExpirableStorage;
use JSON\DataObject;
use Mapper\DB\UserMapper;
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
     * @param UserMapper $userMapper
     * @param MailerService $mailer
     * @param ExpirableStorage $storage
     * @param SessionManager $sessionManager
     * @param Credentials $credentials
     * @param Facebook $facebook
     * @param PasswordGeneratorInterface $pwdGenerator
     */
    public function __construct(
        UserMapper $userMapper,
        MailerService $mailer,
        ExpirableStorage $storage,
        SessionManager $sessionManager,
        Credentials $credentials,
        Facebook $facebook,
        PasswordGeneratorInterface $pwdGenerator
    )
    {
        $this->userMapper = $userMapper;
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
        $user = $this->userMapper->fetchById($userId);
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
        $user
            ->setEmail($json->get('email', 'string'))
            ->setPassword($json->get('password', 'string'))
            ->setFirstName($json->get('firstName', 'string'))
            ->setLastName($json->get('lastName', 'string'))
            ->setPicture($json->get('picture', 'string', null, ''));

        if ($this->userMapper->emailExists($user->getEmail())) {
            throw ApiException::create(ApiException::USER_EXISTS);
        }
        $this->userMapper->insert($user);
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
        if (false === $this->userMapper->emailExists($email)) {
            $this->userMapper->confirmEmail($email);
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
        $user = $this->userMapper->fetchByEmailAndPassword($email, $password);
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
        $user = $this->userMapper->fetchByEmail($fbUser->getEmail());
        if (null === $user) {
            $pic = $fbUser->getPicture();
            $user = new User();
            $user
                ->setEmail($fbUser->getEmail())
                ->setFirstName($fbUser->getFirstName())
                ->setLastName($fbUser->getLastName())
                ->setPicture($pic ? $pic->getUrl() : null)
                ->setPassword($this->pwdGenerator->generatePassword());
            $this->userMapper->insert($user);
        }
        $token = $this->sessionManager->createSession($user->getId(), $request);
        return new JsonResponse($token);
    }

    public function showPasswordChangeForm(Request $request)
    {
        return 'hello';
    }
}