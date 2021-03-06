<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;

use GuzzleHttp;
use Voyager;
use Jenssegers\Agent\Agent;
use App\ActionLog;
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
        $agent = new Agent();
        if ($agent->isMobile() || $agent->isTablet()) {
            dispatch(new LogAction(ActionLog::ACTION_USER_REGISTERED_MOBILE, json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip()));
        } else {
            dispatch(new LogAction(ActionLog::ACTION_USER_REGISTERED, json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip()));
        }
        
        //创建默认的收藏夹
        $bookmark = new \App\Bookmark;
        $bookmark->uid = $user->id;
        $bookmark->name = \App\Bookmark::DEFAULT;
        $bookmark->default = 1;
        $bookmark->save();
        //需求变更，弃用
        /*$domain = env('APP_URL');
        //注册和社交登录的统计代码是一样的，请求同一个页面就行了，仅仅是query参数不同
        $url = $domain . 'socialiteStat.html?query=email';
        $client = new GuzzleHttp\Client();
        $res = $client->requestAsync('GET', $url);
        return redirect('welcome?socialte=email');*/
    }
}
