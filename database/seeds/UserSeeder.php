<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Users;
use App\Models\Roles;
use App\Models\Permissions;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //清空表
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //用户
        $user = Users::firstOrCreate([
            'username' => 'root',
            'nickname' => '超级管理员',
            'mobile' => '13800000000',
            'email' => 'root@root.com',
            'password' => '123456',
        ]);

        //角色
        $role = Roles::firstOrCreate([
            'name' => 'root',
            'guard_name' => 'web',
            'display_name' => '超级管理员'
        ]);

        //权限
        $permissions = [
            ['pmsn.system', 'web', '系统管理', '', 'fa-address-book', 0, 2,
                'child' => [
                    ['pmsn.system.permissions', 'web', '权限管理', 'system.permissions', 'fa-address-card', 7, 2],
                    ['pmsn.system.users', 'web', '用户管理', 'system.users', 'fa-user', 10, 2],
                    ['pmsn.system.roles', 'web', '角色管理', 'system.roles', 'fa-user-secret', 9, 2],
                    ['pmsn.system.config', 'web', '配置管理', 'system.configs', 'fa-wrench', 6, 2],
                    ['pmsn.system.logs', 'web', '日志管理', 'system.logs', 'fa-list-alt', 5, 2]
                ]
            ],
            [
                'pmsn.mp.base', 'web', '公众号基础功能', '', 'fa-align-justify', 10, 2,
                'child' => [
                    ['pmsn.mp.base.welcomes', 'web', '关注欢迎语', 'mp.base.welcomes', 'fa-commenting-o', 9, 2],
                    ['pmsn.mp.base.keywords', 'web', '关键字回复', 'mp.base.keywords', 'fa-comments-o', 7, 2],
                    ['pmsn.mp.base.menus', 'web', '自定义菜单', 'mp.base.menus', 'fa-th-list', 8, 2],
                    ['pmsn.mp.base.fans', 'web', '粉丝管理', 'mp.base.fans', 'fa-user-secret', 6, 2],
                    ['pmsn.mp.base.info', 'web', '公众号配置', 'mp.base.info', 'fa-wechat', 10, 2],
                ]
            ],
            [
                'pmsn.mp.advanced', 'web', '公众号高级功能', '', 'fa-align-justify', 9, 2,
                'child' => [
                    ['pmsn.mp.advanced.templates', 'web', '模板消息', 'mp.advanced.templates', 'fa-envelope-o', 0, 2],
                    ['pmsn.mp.advanced.channel_codes', 'web', '渠道二维码', 'mp.advanced.channel_codes', 'fa-qrcode', 0, 2],
                    ['pmsn.mp.advanced.short_urls', 'web', '长链接转短链接', 'mp.advanced.short_urls', 'fa-unlink', 0, 2],
                ]
            ],
            [
                'pmsn.work.base', 'web', '企业微信基础功能', '', 'fa-align-justify', 8, 2,
                'child' => [
                    ['pmsn.work.base.users', 'web', '成员管理', 'work.base.users', 'fa-user-o', 4, 2],
                    ['pmsn.work.base.info', 'web', '企业微信配置', 'work.base.info', 'fa-wechat', 5, 2],
                    ['pmsn.work.base.customers', 'web', '客户管理', 'work.base.customers', 'fa-address-book-o', 2, 2],
                    ['pmsn.work.base.welcomes', 'web', '加好友欢迎语', 'work.base.welcomes', 'fa-commenting-o', 3, 2],
                    ['pmsn.work.base.group_chats', 'web', '客户群管理', 'work.base.group_chats', 'fa-group', 0, 2],
                ]
            ],
            [
                'pmsn.word.advanced', 'web', '企业微信高级功能', '', 'fa-align-justify', 7, 2,
                'child' => [
                    ['pmsn.work.advanced.channel_codes', 'web', '渠道二维码', 'work.advanced.channel_codes', 'fa-qrcode', 3, 2],
                ]
            ]
        ];
        $permissionIds = [];
        foreach ($permissions as $p1) {
            list($name, $guardName, $displayName, $routeName, $icon, $sort, $type) = $p1;
            $permission1 = Permissions::firstOrCreate([
                'pid' => 0,
                'name' => $name,
                'guard_name' => $guardName,
                'display_name' => $displayName,
                'route_name' => $routeName,
                'icon' => $icon,
                'sort' => $sort,
                'type' => $type
            ]);
            $permissionIds[] = $permission1->id;
            $p1Child = $p1['child'] ?? [];
            foreach ($p1Child as $p2) {
                list($name, $guardName, $displayName, $routeName, $icon, $sort, $type) = $p2;
                $p = Permissions::firstOrCreate([
                    'pid' => $permission1->id,
                    'name' => $name,
                    'guard_name' => $guardName,
                    'display_name' => $displayName,
                    'route_name' => $routeName,
                    'icon' => $icon,
                    'sort' => $sort,
                    'type' => $type
                ]);
                $permissionIds[] = $p->id;
            }
        }

        //为用户添加角色
        $role->syncPermissions($permissionIds);
        $user->assignRole($role);
    }
}
