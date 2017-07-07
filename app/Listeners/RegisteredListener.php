<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;

use GuzzleHttp;
use Voyager;
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

        $domain = env('APP_URL');
        //注册和社交登录的统计代码是一样的，请求同一个页面就行了，仅仅是query参数不同
        $url = $domain . 'socialiteStat.html?query=email';
        $client = new GuzzleHttp\Client();
        $res = $client->requestAsync('GET', $url);
    }
}
