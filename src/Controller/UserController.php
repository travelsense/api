<?php

namespace Controller;

use Api\Request\EmailPasswordLoginRequest;
use Api\Request\FacebookTokenLoginRequest;
use Api\Request\RegistrationRequest;
use Exception\ApiException;
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Mapper\UserMapper;
use Security\Authentication\Credentials;
use Security\SessionManager;
use Security\TokenManager;
use Service\Mailer\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use User;

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
     * @param TokenManager $secureToken
     * @param SessionManager $sessionManager
     * @param Credentials $credentials
     * @param Facebook $facebook
     * @param PasswordGeneratorInterface $pwdGenerator
     */
    public function __construct(
        UserMapper $userMapper,
        MailerService $mailer,
        TokenManager $secureToken,
        SessionManager $sessionManager,
        Credentials $credentials,
        Facebook $facebook,
        PasswordGeneratorInterface $pwdGenerator
    )
    {
        $this->userMapper = $userMapper;
        $this->mailer = $mailer;
        $this->tokenManager = $secureToken;
        $this->sessionManager = $sessionManager;
        $this->credentials = $credentials;
        $this->facebook = $facebook;
        $this->pwdGenerator = $pwdGenerator;
    }

    /**
     * Start email-based registration. Send a confirmation email.
     * @param RegistrationRequest $request
     * @return array
     * @throws ApiException
     * @internal param array $payload
     */
    public function startRegisterThroughEmail(RegistrationRequest $request)
    {
        if ($this->userMapper->emailExists($request->email)) {
            throw ApiException::create(ApiException::USER_EXISTS);
        }

        $token = $this->tokenManager->encrypt($request);
        $this->mailer->sendAccountConfirmationMessage($request->email, $token);
        return [];
    }

    /**
     * @param string $token
     * @return Response
     */
    public function finishRegisterThroughEmail($token)
    {
        /** @var RegistrationRequest $request */
        $request = $this->tokenManager->decrypt($token);
        if ($request === null) {
            return new Response('Invalid token.');
        }
        if (false === $this->userMapper->emailExists($request->email)) {
            $user = new User();
            $user
                ->setEmail( $request->email)
                ->setPassword( $request->password)
                ->setFirstName( $request->firstName)
                ->setLastName( $request->lastName)
                ->setPicture( $request->picture);
            $this->userMapper->insert($user);
        }
        return new Response('Account has been created.');
    }

    /**
     * @param EmailPasswordLoginRequest $loginRequest
     * @param Request $request
     * @return array
     * @throws ApiException
     * @internal param array $payload
     */
    public function loginByEmailAndPassword(EmailPasswordLoginRequest $loginRequest, Request $request)
    {
        $user = $this->userMapper->fetchByEmailAndPassword($loginRequest->email, $loginRequest->password);
        if (null === $user) {
            throw ApiException::create(ApiException::INVALID_EMAIL_PASSWORD);
        }
        return $this->login($user['id'], $request);
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
     * @param FacebookTokenLoginRequest $loginRequest
     * @param Request $request
     * @return array
     */
    public function loginByFacebook(FacebookTokenLoginRequest $loginRequest, Request $request)
    {
        $this->facebook->setDefaultAccessToken($loginRequest->token);
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
        return $this->login($user->getId(), $request);
    }

    public function showPasswordChangeForm(Request $request)
    {
        return 'hello';
    }

    /**
     * @param $userId
     * @param Request $request
     * @return array
     */
    private function login($userId, Request $request)
    {
        $device = $request->headers->get('User-Agent');
        $token = $this->sessionManager->createSession($userId, $device);
        return [
            'token' => $token,
        ];
    }
}