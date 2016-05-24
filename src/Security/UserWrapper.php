<?php
namespace Security;

use Api\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserWrapper implements UserInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserWrapper constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->user->getPassword();
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->user->getId();
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }
}
