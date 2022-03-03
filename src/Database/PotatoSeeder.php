<?php
namespace YK\Basic\Database;

use Illuminate\Database\Seeder;
use YK\Basic\Extensions\AdminUserFactory;
use YK\Basic\Models\Menu;
use YK\Basic\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;

class BasicSeeder extends Seeder
{

    private $permissions = [
        [
            'name' => 'admin-user.index',
            'display_name' => '用户列表',
            'pg_id' => 2
        ],
        [
            'name' => 'admin-user.show',
            'display_name' => '查看用户',
            'pg_id' => 2
        ],
        [
            'name' => 'admin-user.store',
            'display_name' => '新建用户',
            'pg_id' => 2
        ],
        [
            'name' => 'admin-user.update',
            'display_name' => '更新用户',
            'pg_id' => 2
        ],
        [
            'name' => 'admin-user.destroy',
            'display_name' => '删除用户',
            'pg_id' => 2
        ],
        [
            'name' => 'admin-user.roles',
            'display_name' => '用户角色',
            'pg_id' => 2
        ],
        [
            'name' => 'admin-user.assign-roles',
            'display_name' => '分配角色',
            'pg_id' => 2
        ],
        [
            'name' => 'role.index',
            'display_name' => '角色列表',
            'pg_id' => 3
        ],
        [
            'name' => 'role.show',
            'display_name' => '查看角色',
            'pg_id' => 3
        ],
        [
            'name' => 'role.store',
            'display_name' => '新建角色',
            'pg_id' => 3
        ],
        [
            'name' => 'role.update',
            'display_name' => '更新角色',
            'pg_id' => 3
        ],
        [
            'name' => 'role.destroy',
            'display_name' => '删除角色',
            'pg_id' => 3
        ],
        [
            'name' => 'role.permissions',
            'display_name' => '角色权限查看',
            'pg_id' => 3
        ],
        [
            'name' => 'role.assign-permissions',
            'display_name' => '角色分配权限',
            'pg_id' => 3
        ],
        /* delete by nash 20200417 设置成public的路由
        [
            'name' => 'role.guard-name-roles',
            'display_name' => 'guard设置',
            'pg_id' => 3
        ],
        [
            'name' => 'permission-group.guard-name-for-permission',
            'display_name' => 'Guard name for permissions',
            'pg_id' => 3
        ],*/
        [
            'name' => 'permission.index',
            'display_name' => '权限列表',
            'pg_id' => 4
        ],
        [
            'name' => 'permission.show',
            'display_name' => '查看权限',
            'pg_id' => 4
        ],
        [
            'name' => 'permission.store',
            'display_name' => '新增权限',
            'pg_id' => 4
        ],
        [
            'name' => 'permission.update',
            'display_name' => '更新权限',
            'pg_id' => 4
        ],
        [
            'name' => 'permission.destroy',
            'display_name' => '删除权限',
            'pg_id' => 4
        ],
        [
            'name' => 'permission.all-user-permission',
            'display_name' => 'All permissions of the user',
            'pg_id' => 4
        ],
        [
            'name' => 'menu.index',
            'display_name' => '菜单列表',
            'pg_id' => 5
        ],
        [
            'name' => 'menu.my',
            'display_name' => '获取我的菜单',
            'pg_id' => 5
        ],
        [
            'name' => 'menu.show',
            'display_name' => '查看菜单',
            'pg_id' => 5
        ],
        [
            'name' => 'menu.store',
            'display_name' => '新增菜单',
            'pg_id' => 5
        ],
        [
            'name' => 'menu.update',
            'display_name' => '更新菜单',
            'pg_id' => 5
        ],
        [
            'name' => 'menu.destroy',
            'display_name' => '删除菜单',
            'pg_id' => 5
        ],
        [
            'name' => 'permission-group.index',
            'display_name' => '权限组列表',
            'pg_id' => 6
        ],
        [
            'name' => 'permission-group.show',
            'display_name' => '查看权限组',
            'pg_id' => 6
        ],
        [
            'name' => 'permission-group.store',
            'display_name' => '新增权限组',
            'pg_id' => 6
        ],
        [
            'name' => 'permission-group.update',
            'display_name' => '更新权限组',
            'pg_id' => 6
        ],
        [
            'name' => 'permission-group.destroy',
            'display_name' => '删除权限组',
            'pg_id' => 6
        ],
        [
            'name' => 'param.index',
            'display_name' => '参数列表',
            'pg_id' => 7
        ],
        [
            'name' => 'param.store',
            'display_name' => '新建参数',
            'pg_id' => 7
        ],
        [
            'name' => 'param.update',
            'display_name' => '更新参数',
            'pg_id' => 7
        ],
        [
            'name' => 'param.destroy',
            'display_name' => '删除参数',
            'pg_id' => 7
        ],[
            'name' => 'param.batchdestroy',
            'display_name' => '批量删除参数',
            'pg_id' => 7
        ],
        [
            'name' => 'dictionary.index',
            'display_name' => '字典列表',
            'pg_id' => 8
        ],
        [
            'name' => 'dictionary.store',
            'display_name' => '字典新建',
            'pg_id' => 8
        ],
        [
            'name' => 'dictionary.update',
            'display_name' => '字典更新',
            'pg_id' => 8
        ],
        [
            'name' => 'dictionary.destroy',
            'display_name' => '字典删除',
            'pg_id' => 8
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @author nash< >
     * @return void
     */
    public function run()
    {
        app()['cache']->forget('spatie.permission.cache');

        $this->createdAdminUser();

        $this->createPermissionGroup();

        $this->createRole();

        $this->createPermission();

        $this->createMenu();

        $this->associateRolePermissions();
    }

    /**
     * @author nash< >
     */
    private function createdAdminUser()
    {
        AdminUserFactory::adminUser()->truncate();

        AdminUserFactory::adminUser()->create([
            'username' => 'admin',
            'uuid'=>'1613fda98db811e9b0fc000c2972501c',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'mobile'=>'',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        ]);
    }

    /**
     * @author nash< >
     */
    private function createPermission()
    {
        Permission::query()->delete();

        foreach ($this->permissions as $permission) {
            $permission['guard_name'] = config('admin.super_admin.guard');
            Permission::create($permission);
        }
    }

    /**
     * @author nash< >
     */
    private function createPermissionGroup()
    {
        PermissionGroup::truncate();
        PermissionGroup::insert([
            [
                'id'    => 1,
                'name'  => '系统管理',
                'parent_id'=>0,
                'parent_ids' => '',
                'sort' =>0,
                'set_by_admin'=>0
            ],
            [
                'id'    => 2,
                'name'  => '用户管理',
                'parent_id' => 1,
                'parent_ids' => '1',
                'sort' =>1,
                'set_by_admin'=>0
            ], [
                'id'    => 3,
                'name'  => '角色管理',
                'parent_id' => 1,
                'parent_ids' => '1',
                'sort' =>2,
                'set_by_admin'=>0
            ], [
                'id'    => 4,
                'name'  => '权限管理',
                'parent_id' => 1,
                'parent_ids' => '1',
                'sort' =>3,
                'set_by_admin'=>1
            ], [
                'id'    => 5,
                'name'  => '菜单管理',
                'parent_id' => 1,
                'parent_ids' => '1',
                'sort' =>4,
                'set_by_admin'=>1
            ], [
                'id'    => 6,
                'name'  => '权限组管理',
                'parent_id' => 1,
                'parent_ids' => '1',
                'sort' =>5,
                'set_by_admin'=>1
            ], [
                'id'    => 7,
                'name'  => '参数管理',
                'parent_id' => 1,
                'parent_ids' => '1',
                'sort' =>6,
                'set_by_admin'=>0
            ], [
                'id'    => 8,
                'name'  => '字典管理',
                'parent_id' => 1,
                'parent_ids' => '1',
                'sort' =>7,
                'set_by_admin'=>0
            ]
        ]);
    }

    /**
     * @author nash< >
     */
    private function createRole()
    {
        Role::query()->delete();
        Role::create([
            'name' => '系统管理员',
            'guard_name' => config('admin.super_admin.guard')
        ]);
    }

    /**
     * @author nash< >
     */
    private function createMenu()
    {
        Menu::truncate();
        Menu::insert([
            [
                'id'        => 1,
                'parent_id' => 0,
                'uri'       => '/admin/dashboard',
                'name'      => 'Dashboard',
                'icon'      => 'fa fa-lg fa-tachometer',
                'permission_name'=> '',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 2,
                'parent_id' => 0,
                'uri'       => '/admin/root',
                'name'      => '系统管理',
                'icon'      => 'fa fa-lg fa-cogs',
                'permission_name'=> '',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 3,
                'parent_id' => 2,
                'uri'       => '/admin/admin-user',
                'name'      => '用户管理',
                'icon'      => '',
                'permission_name'=> 'admin-user.index',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 4,
                'parent_id' => 2,
                'uri'       => '/admin/role',
                'name'      => '角色管理',
                'icon'      => '',
                'permission_name'=> 'role.index',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 5,
                'parent_id' => 2,
                'uri'       => '/admin/permission',
                'name'      => '权限管理',
                'icon'      => '',
                'permission_name'=> 'permission.index',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 6,
                'parent_id' => 2,
                'uri'       => '/admin/permission-group',
                'name'      => '权限组管理',
                'icon'      => '',
                'permission_name'=> 'permission-group.index',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 7,
                'parent_id' => 2,
                'uri'       => '/admin/menu',
                'name'      => '菜单管理',
                'icon'      => '',
                'permission_name'=> 'menu.index',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 8,
                'parent_id' => 2,
                'uri'       => '/admin/param',
                'name'      => '参数管理',
                'icon'      => '',
                'permission_name'=> 'param.index',
                'guard_name'=> config('admin.super_admin.guard')
            ],
            [
                'id'        => 9,
                'parent_id' => 2,
                'uri'       => '/admin/dictionary',
                'name'      => '字典管理',
                'icon'      => '',
                'permission_name'=> 'dictionary.index',
                'guard_name'=> config('admin.super_admin.guard')
            ],

        ]);
    }

    /**
     * @author nash< >
     */
    private function associateRolePermissions()
    {
        $role = Role::first();

        AdminUserFactory::adminUser()->first()->assignRole($role->name);

        foreach ($this->permissions as $permission) {
            $role->givePermissionTo($permission['name']);
        }
    }
}
