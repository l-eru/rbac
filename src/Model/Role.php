<?php
/**
 * Created by PhpStorm.
 * User: l-eru
 * Date: 2018/6/3
 * Time: 19:16
 */

namespace L\Rbac\Model;


use L\Rbac\Exception\RbacException;
use Phalcon\Mvc\Model;

class Role extends Model
{
    /**
     * @var int $id 主键ID, 自增
     */
    private $id;

    /**
     * @var string $name 角色名称
     */
    private $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * 将角色与权限进行关联
     *
     * @param array $newAccess
     * @throws RbacException
     */
    public function associateAccess(array $newAccess): void
    {
        /**
         * @var Model\Transaction\Manager $transaction
         */
        $transaction = $this->getDI()->getTransaction();

        $transaction->get();

        // 获取当前角色所绑定的权限列表
        $roleAssociatedAccess = RoleAccess::getRoleAssociatedAccess($this->id);


        /**
         * 1. 获取角色需要进行解绑的权限列表
         * 2. 从数据库中删除
         */
        $untieAccess = array_diff($roleAssociatedAccess, $newAccess);

        if (!RoleAccess::untieRoleAssociatedAccess($untieAccess)) {
            $transaction->rollback();
            throw new RbacException('解绑角色关联的权限列表失败!');
        }


        /**
         * 1. 获取用户需要进行关联的角色列表
         * 2. 新增到数据库中
         */
        $associateAccess = array_diff($newAccess, $roleAssociatedAccess);

        foreach ($associateAccess as $access) {
            $roleAccess = new RoleAccess();

            $roleAccess->setRoleId($this->id);
            $roleAccess->setAccessId($access);

            if (!$roleAccess->create()) {
                $transaction->rollback();
                throw new RbacException('新增角色关联的权限列表失败!');
            }
        }

        $transaction->commit();
    }


}