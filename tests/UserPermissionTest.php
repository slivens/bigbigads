<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;

class UserPermissionTest extends TestCase
{
     use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = $this->fakeUser();
        //基本的添加删除测试
        $this->assertTrue(!$user->can('monitor_support'));
        $user->addPermission('monitor_support');
        $this->assertTrue($user->can('monitor_support'));

        $user->delPermission('monitor_support');
        $this->assertTrue(!$user->can('monitor_support'));

        //过期测试,一个月就过期的权限和一个月后就过期的权限
        $user->addPermission('monitor_ad_keyword', Carbon::now()->subMonth());
        $this->assertTrue(!$user->can('monitor_ad_keyword'));

        $user->addPermission('monitor_ad_keyword', Carbon::now()->addMonth());
        $this->assertTrue($user->can('monitor_ad_keyword'));

        //策略替换测试
        $user->addUserUsage('monitor_ad_keyword', 20, 1);
        $item = $user->getUsage('monitor_ad_keyword');
        //echo json_encode($item);
        if (intval($item[1]) != 20 || $item[2] != 1) {
            $this->assertTrue(false);
        }

        //从前端角色获取用户信息，确认有包含新增的权限和策略
        $this->actingAs($user)->json('GET', '/userinfo_web')->seeJsonStructure(['permissions' => ['monitor_ad_keyword']]);
        //TODO：策略的检查出错
        /* $this->actingAs($user)->json('GET', '/userinfo')->seeJson(['user' => ['usage' => ['monitor_ad_keyword' => $item]]]); */

    }
}
