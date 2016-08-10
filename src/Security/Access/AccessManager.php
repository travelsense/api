<?php
namespace Api\Security\Access;

use Api\Mapper\DB\User\RoleMapper;
use Api\Model\Travel\Travel;
use Api\Model\User;
use InvalidArgumentException;

class AccessManager
{
    /**
     * @var RoleMapper
     */
    private $roleMapper;

    /**
     * AccessManager constructor.
     * @param RoleMapper $roleMapper
     */
    public function __construct(RoleMapper $roleMapper)
    {
        $this->roleMapper = $roleMapper;
    }

    /**
     * Is $actor granted permission to take $action on $subject
     * @param ActorInterface        $actor
     * @param string                $action
     * @param SubjectInterface|null $subject
     * @return bool
     */
    public function isGranted(ActorInterface $actor, string $action, SubjectInterface $subject = null): bool
    {
        switch ($action) {
            case Action::READ:
                return true;
            case Action::WRITE:
                if ($actor instanceof User && $subject instanceof Travel) {
                    return $this->hasWritePermission($actor, $subject);
                }
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unknown action: %s', $action));
        }
        return false;
    }

    /**
     * @param User   $user
     * @param Travel $travel
     * @return bool
     */
    private function hasWritePermission(User $user, Travel $travel): bool
    {
        if ($user->getId() === $travel->getAuthorId()) {
            return true;
        }
        $roles = $this->roleMapper->getRoles($user->getId());
        return in_array(Role::MODERATOR, $roles, true);
    }
}
