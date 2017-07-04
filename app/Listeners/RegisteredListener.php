<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;

use GuzzleHttp;

class RegisteredListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $user = $event->user;
        dispatch(new LogAction("USER_REGISTERED", json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip()));
        //创建默认的收藏夹
        $bookmark = new \App\Bookmark;
        $bookmark->uid = $user->id;
        $bookmark->name = "default";
        $bookmark->save();

        /*
            EMAIL_VERIFICATION=true  表示为开启邮箱验证
            EMAIL_VERIFICATION=false 表示为关闭邮箱验证
            当关闭邮箱验证时，采用GuzzleHttp请求来加载谷歌统计代码
        */
        $emailVerification = env('EMAIL_VERIFICATION');
        $domain = env('APP_URL');
        if (!$emailVerification) {
            $url = $domain . 'registerStatistics.html';
            $client = new GuzzleHttp\Client();
            $res = $client->request('GET', $url);
        }
    }
}
