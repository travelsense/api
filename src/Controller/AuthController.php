<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\JSON\DataObject;
use Api\Mapper\DB\UserMapper;
use Api\Model\User;
use Api\Security\SessionManager;
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Psr\Log\LoggerAwareTrait;
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
     *
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
    ) {
        $this->userMapper = $userMapper;
        $this->sessionManager = $sessionManager;
        $this->facebook = $facebook;
        $this->pwdGenerator = $pwdGenerator;
    }

    /**
     * @param Request $request ['email' => 'a@b.com', 'password => '123'] or ['facebook_token' => '123']
     * @return JsonResponse ['token' => $token]
     * @throws ApiException
     */
    public function create(Request $request)
    {
        $json = DataObject::createFromString($request->getContent());
        if ($this->logger) {
            $this->logger->debug('New token requested', ['request' => $json->getRootObject()]);
        }
        if ($json->has('fbToken')) {
            $user = $this->getUserByFacebookToken($json->getString('fbToken'));
        } else {
            $email = $json->getEmail('email');
            $password = $json->getString('password');
            $user = $this->getUserByEmailPassword($email, $password);
        }
        $token = $this->sessionManager->createSession($user->getId(), $request);
        return new JsonResponse(['token' => $token]);
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     * @throws ApiException
     */
    protected function getUserByEmailPassword($email, $password)
    {
        $user = $this->userMapper->fetchByEmailAndPassword($email, $password);
        if (null === $user) {
            throw ApiException::create(ApiException::INVALID_EMAIL_PASSWORD);
        }
        return $user;
    }

    /**
     * @param string $fbToken
     * @return User
     */
    protected function getUserByFacebookToken($fbToken)
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
        return $user;
    }
}
