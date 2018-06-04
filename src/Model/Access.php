<?php
/**
 * Created by PhpStorm.
 * User: l-eru
 * Date: 2018/6/3
 * Time: 19:16
 */

namespace L\Rbac\Model;


use Phalcon\Mvc\Model;

class Access extends Model
{
    /**
     * @var int $id 主键ID, 自增
     */
    private $id;

    /**
     * @var int $pid 父权限ID, 保留
     */
    private $pid;

    /**
     * @var string $name 权限名称
     */
    private $name;

    /**
     * @var string $controller 权限对应的控制器名称
     */
    private $controller;

    /**
     * @var string $action 权限对应的方法名称
     */
    private $action;


    /**
     * @var int $menu_id 菜单ID, 外键， 与menu表1对1
     */
    private $menu_id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     */
    public function setPid(int $pid): void
    {
        $this->pid = $pid;
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
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return int
     */
    public function getMenuId(): int
    {
        return $this->menu_id;
    }

    /**
     * @param int $menu_id
     */
    public function setMenuId(int $menu_id): void
    {
        $this->menu_id = $menu_id;
    }

    /**
     * 根据权限列表获取对应的菜单数据 1vs1
     *
     * @param array $roleAssociatedAccess
     * @return array
     */
    public static function getAccessAssociatedMenu(array $roleAssociatedAccess): array
    {
        return array_column(self::getAccessByRole($roleAssociatedAccess), 'menu_id');
    }

    /**
     * @param array $roleAssociatedAccess
     * @return array
     */
    public static function getAccessByRole(array $roleAssociatedAccess): array
    {
        return Access::find([
            'id in ({id:array})',
            'bind' => [
                'id' => $roleAssociatedAccess
            ]
        ])->toArray();
    }
}