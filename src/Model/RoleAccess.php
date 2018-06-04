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

class RoleAccess extends Model
{
    /**
     * @var int $role_id 角色ID
     */
    private $role_id;

    /**
     * @var int $access_id 与角色关联的权限ID
     */
    private $access_id;

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
     * @return int
     */
    public function getAccessId(): int
    {
        return $this->access_id;
    }

    /**
     * @param int $access_id
     */
    public function setAccessId(int $access_id): void
    {
        $this->access_id = $access_id;
    }

    /**
     * 获取角色所关联的权限列表
     *
     * @param int $roleId 对应角色ID
     * @return array 对应角色所关联的权限列表
     */
    public static function getRoleAssociatedAccess(int $roleId): array
    {
        $roleAssociatedAccess = RoleAccess::find([
            'access_id' => $roleId
        ])->toArray();

        return array_column($roleAssociatedAccess, 'access_id');
    }

    /**
     * 删除与对应角色关联的权限列表。 该方法需要放在事务中进行执行，确保数据的完整性。
     *
     * @param array $access 需要删除的权限列表ID
     * @return bool 如果需要删除的列表为空或删除成功，则返回真. 删除失败返回false
     */
    public static function untieRoleAssociatedAccess(array $access): bool
    {
        if (empty($access)) return true;

        $accessIdLists = implode(',', $access);

        return Di::getDefault()->getModelsManager()
            ->createQuery('DELETE FROM ' . self::class . ' WHERE access_id in (' . $accessIdLists . ')')
            ->execute()->success();
    }
}