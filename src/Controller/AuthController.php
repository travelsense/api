<?php
namespace Controller;

use Exception\ApiException;
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Mapper\DB\UserMapper;
use Model\User;
use Psr\Log\LoggerAwareTrait;
use Security\SessionManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auth API controller
 */
class AuthController
{
    use LoggerAwareTrait;

    /**
     * @var PasswordGeneratorInterface
     */
    private $pwdGenerator;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * UserSessionController constructor.
     * @param UserMapper $userMapper
     * @param SessionManager $sessionManager
     * @param Facebook $facebook
     * @param PasswordGeneratorInterface $pwdGenerator
     */
    public function __construct(
        UserMapper $userMapper,
        SessionManager $sessionManager,
        Facebook $facebook,
        PasswordGeneratorInterface $pwdGenerator
    )
    {
        $this->userMapper = $userMapper;
        $this->sessionManager = $sessionManager;
        $this->facebook = $facebook;
        $this->pwdGenerator = $pwdGenerator;
    }

    /**
     * @param string $email
     * @param Request $request
     * @return JsonResponse
     * @throws ApiException
     */
    public function createTokenByEmail($email, Request $request)
    {
        $password = json_decode($request->getContent());
        if ($this->logger) {
            $this->logger->debug('Password', ['password' => $password]);
        }
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
}
