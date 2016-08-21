<?php
namespace Api\Mapper\DB\User;

use Api\AbstractPDOMapper;
use BadMethodCallException;

/**
 * Class RoleMapper
 * @package Api\Mapper\DB\User
 */
class RoleMapper extends AbstractPDOMapper
{
    /**
     * Get user roles
     * @param int $user_id
     * @return string[]
     */
    public function getRoles(int $user_id): array
    {
        $select = $this->pdo->prepare('SELECT role FROM user_roles WHERE user_id = :user_id ORDER BY role ASC');
        $select->execute([
            ':user_id' => $user_id,
        ]);
        $roles = [];
        while (false !== $role = $select->fetchColumn()) {
            $roles[$role] = $role;
        }
        return $roles;
    }

    /**
     * Grant the user the role
     * @param int    $user_id
     * @param string $role
     */
    public function grantRole(int $user_id, string $role)
    {
        $insert = $this->pdo->prepare('INSERT INTO user_roles (user_id, role) VALUES (:user_id, :role)');
        $insert->execute([
            ':user_id' => $user_id,
            ':role'    => $role,
        ]);
    }

    /**
     * Withdraw the role from the user
     * @param int    $user_id
     * @param string $role
     */
    public function withdrawRole(int $user_id, string $role)
    {
        $delete = $this->pdo->prepare('DELETE FROM user_roles WHERE user_id = :user_id AND role = :role');
        $delete->execute([
            ':user_id' => $user_id,
            ':role'    => $role,
        ]);
    }

    /**
     * Create object by a DB row
     *
     * @param  array $row
     * @return mixed
     */
    protected function create(array $row)
    {
        throw new BadMethodCallException();
    }
}
