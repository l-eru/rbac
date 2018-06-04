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

class User extends Model
{
    /**
     * @var int $id 主键ID, 自增
     */
    private $id;

    /**
     * @var string $username 用户登录账户
     */
    private $username;

    /**
     * @var string $real_name 用户真实姓名
     */
    private $real_name;

    /**
     * @var string $password 用户登录密码
     */
    private $password;

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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getRealName(): string
    {
        return $this->real_name;
    }

    /**
     * @param string $real_name
     */
    public function setRealName(string $real_name): void
    {
        $this->real_name = $real_name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * 用户与角色进行绑定
     *
     * @param array $newRoles 新绑定的角色列表
     * @throws RbacException
     */
    public function associateRoles(array $newRoles): void
    {
        /**
         * @var Model\Transaction\Manager $transaction
         */
        $transaction = $this->getDI()->getTransaction();

        $transaction->get();

        // 获取当前用户所绑定的角色列表
        $userAssociatedRoles = UserRole::getUserAssociatedRoles($this->id);

        /**
         * 1. 获取用户需要进行解绑的角色列表
         * 2. 从数据库中删除
         */
        $untieRoles = array_diff($userAssociatedRoles, $newRoles);

        if (!UserRole::untieUserAssociatedRoles($untieRoles)) {
            $transaction->rollback();
            throw new RbacException('解绑用户关联的角色列表失败!');
        }

        /**
         * 1. 获取用户需要进行关联的角色列表
         * 2. 新增到数据库中
         */
        $associateRoles = array_diff($newRoles, $userAssociatedRoles);

        foreach ($associateRoles as $associateRole) {
            $userRole = new UserRole();

            $userRole->setUserId($this->id);
            $userRole->setRoleId($associateRole);

            if (!$userRole->create()) {
                $transaction->rollback();
                throw new RbacException('新增用户关联的角色列表失败!');
            }
        }

        $transaction->commit();
    }
}