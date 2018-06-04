<?php
/**
 * Created by PhpStorm.
 * User: l-eru
 * Date: 2018/6/3
 * Time: 19:16
 */

namespace L\Rbac\Model;


use Phalcon\Di;
use Phalcon\Mvc\Model;

class UserRole extends Model
{
    /**
     * @var int $user_id 用户id
     */
    private $user_id;

    /**
     * @var int $role_id 与对应用户关联角色id
     */
    private $role_id;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @param int $role_id
     */
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }

    /**
     * 获取对应用户ID所关联的所有角色ID列表
     *
     * @param int $userId 用获取对应关联角色列表的用户ID
     * @return array 返回对应用户所关联的所有角色ID列表
     */
    public static function getUserAssociatedRoles(int $userId): array
    {
        $userAssociateRoles = UserRole::find([
            'user_id' => $userId
        ])->toArray();

        return array_column($userAssociateRoles, 'role_id');
    }

    /**
     * 删除与对应用户关联的角色列表。 该方法需要放在事务中进行执行，确保数据的完整性。
     *
     * @param array $roles 需要删除的角色列表ID
     * @return bool 如果需要删除的列表为空或删除成功，则返回真. 删除失败返回false
     */
    public static function untieUserAssociatedRoles(array $roles): bool
    {
        if (empty($roles)) return true;

        $roleIdLists = implode(',', $roles);

        return Di::getDefault()->getModelsManager()
            ->createQuery('DELETE FROM ' . self::class . ' WHERE role_id in (' . $roleIdLists . ')')
            ->execute()->success();
    }
}