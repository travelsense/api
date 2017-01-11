<?php
namespace Api\Controller;

use Api\Event\UserLoggedWithFacebook;
use Api\Exception\ApiException;
use Api\JSON\DataObject;
use Api\Mapper\DB\UserMapper;
use Api\Model\User;
use Api\Security\SessionManager;
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auth API controller
 */
class AuthController extends ApiController
{
    /**
     * @var PasswordGeneratorInterface
     */
    private $pwd_generator;

    /**
     * @var UserMapper
     */
    private $user_mapper;

    /**
     * @var SessionManager
     */
    private $session_manager;

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * UserSessionController constructor.
     *
     * @param UserMapper                 $user_mapper
     * @param SessionManager             $session_manager
     * @param Facebook                   $facebook
     * @param PasswordGeneratorInterface $pwd_generator
     * @param EventDispatcher            $dispatcher
     */
    public function __construct(
        UserMapper $user_mapper,
        SessionManager $session_manager,
        Facebook $facebook,
        PasswordGeneratorInterface $pwd_generator,
        EventDispatcher $dispatcher
    ) {
        $this->user_mapper = $user_mapper;
        $this->session_manager = $session_manager;
        $this->facebook = $facebook;
        $this->pwd_generator = $pwd_generator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request ['email' => 'a@b.com', 'password => '123'] or ['facebook_token' => '123']
     * @return array  ['token' => $token]
     * @throws ApiException
     */
    public function create(Request $request): array
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
        $token = $this->session_manager->createSession($user->getId(), $request);
        return ['token' => $token];
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     * @throws ApiException
     */
    protected function getUserByEmailPassword(string $email, string $password): User
    {
        $user = $this->user_mapper->fetchByEmailAndPassword($email, $password);
        if (null === $user) {
            throw new ApiException('Invalid email or password', ApiException::INVALID_EMAIL_PASSWORD);
        }
        return $user;
    }

    /**
     * @param string $token
     * @return User
     */
    protected function getUserByFacebookToken(string $token): User
    {
        $this->facebook->setDefaultAccessToken($token);
        $fb_user = $this->facebook
            ->get('/me?fields=picture,email,first_name,last_name')
            ->getGraphUser();
        $user = $this->user_mapper->fetchByEmail($fb_user->getEmail());
        if (null === $user) {
            $user = new User();
            $user
                ->setEmail($fb_user->getEmail())
                ->setFirstName($fb_user->getFirstName())
                ->setLastName($fb_user->getLastName())
                ->setPassword($this->pwd_generator->generatePassword());
            $this->user_mapper->insert($user);
        }
        $pic = $fb_user->getPicture();
        if ($pic) {
            $this->dispatcher->dispatch(
                UserLoggedWithFacebook::NAME,
                new UserLoggedWithFacebook($user->getId(), $pic->getUrl())
            );
        }
        return $user;
    }
}
