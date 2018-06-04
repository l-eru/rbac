<?php
/**
 * Created by PhpStorm.
 * User: l-eru
 * Date: 2018/6/3
 * Time: 19:17
 */

namespace L\Rbac\Model;


use Phalcon\Mvc\Model;

class Menu extends Model
{
    /**
     * @var int $id 主键ID，自增
     */
    private $id;

    /**
     * @var int $pid 父菜单ID
     */
    private $pid;

    /**
     * @var string $name 菜单名称
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
     * 根据权限ID获取对应的菜单列表
     *
     * @param int $roleId
     * @return array
     */
    public static function getMenuLists(int $roleId): array
    {
        // 获取与角色绑定的权限列表
        $roleAssociatedAccess = RoleAccess::getRoleAssociatedAccess($roleId);


        // 获取权限列表对应的菜单列表
        $accessAssociatedMenu = Access::getAccessAssociatedMenu($roleAssociatedAccess);

        // 获取权限对应的菜单
        $menuLists = Menu::find([
            'id in ({id:array})',
            'bind' => [
                'id' => $accessAssociatedMenu
            ]
        ])->toArray();


        // 所有父级分类, 正式部署时应作缓存
        $totalRootMenuLists = Menu::findByPid(0)->toArray();

        /**
         * 遍历所有父级菜单与根据角色权限查找出来的菜单列表，
         * 将对应的子菜单放到父级菜单下， 并删除
         * 如果父级菜单下没有子菜单， 则删除该父级菜单
         */
        foreach ($totalRootMenuLists as $rootKey => $rootMenu) {
            foreach ($menuLists as $menuKey => $menu) {
                if ($rootMenu['id'] == $menu['pid']) {
                    $totalRootMenuLists[$rootKey]['list'][] = $menu;
                    unset($menuLists[$menuKey]);
                }
            }

            if (empty($totalRootMenuLists[$rootKey]['list'])) unset($totalRootMenuLists[$rootKey]);
        }

        return $totalRootMenuLists;
    }
}