<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;
use Session;
use Illuminate\Contracts\Cache\Repository as CacheContract;

/**
 * 用于统计在线用户的实现相对比较粗糙，需要对Laravel的框架中的服务更熟悉才能直接调用相关对象，否则就只能按照当前实现只使用文档中提供的服务，未提到的就原始实现。
 */
class SessionService implements \App\Contracts\SessionService
{
    /* protected $cache; */
    private $sessions;

    public function __construct()
    {
    }

    /**
     * @{inheritDoc}
     */
    public function sessionInfos() : Collection
    {
        if ($this->sessions)
            return $this->sessions;
        $keys = Redis::keys('laravel:session.*');
        $result = new Collection();

        foreach ($keys as $key) {
            $session = $this->session($key);
            if (!$session || !isset($session['session_statics']))
                continue;
            $result[$key] = $session['session_statics'];    
        }
        $this->sessions = $result;
        return $result;
    }

    /**
     * @{inheritDoc}
     */
    public function session($sessionId)
    {
        $sessionStr = Redis::get($sessionId);
        if (!$sessionStr)
            return null;
        // Session->Handler->Cache，序列化了2次，所以反序列化也要2次
        // 这个是通过阅读底层实现所得出的结论，正如类描述所说，依赖于实现获取数据是不好的行为
        // 应该看下是否可以以较低代价创建Illuminate\Session\Store，然后从Store中获取数据。
        $session = @unserialize($sessionStr);
        return @unserialize($session);
    }

    /**
     * @{inheritDoc}
     */
    public function userInfos() : Collection
    {
        $sessions = $this->sessionInfos();
        $infos = new Collection();
        foreach ($sessions as $key => $statics) {
            $subSet = $infos->get($statics['email'], []);
            $subSet[$key] = $statics;
            $infos[$statics['email']] = $subSet;
        }
        return $infos;
    }

    /**
     * @{inheritDoc}
     */
    public function removeSession(string $sessionId)
    {
    }


    public function removeUserSessions(string $email, int $reserved = 0)
    {
    }
}
