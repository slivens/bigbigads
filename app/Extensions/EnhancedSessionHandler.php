<?php

namespace App\Extensions;

/* use Illuminate\Support\Collection; */
use Illuminate\Session\CacheBasedSessionHandler;
use Carbon\Carbon;
use Auth;
use Log;
/**
 * EnhancedSession相当于redis+database同时使用，并往每个session添加一些统计信息
 *
 * redis用于用户实际使用场景；
 * database用于日常管理查看在线用户和对用户执行踢出等操作，不应该影响线上系统的效率，所以所有操作都应该在队列中完成。
 * @TODO 该扩展将会被封装进bba/common包
 * @TODO 发出event事件，将session同时写到database中，分析直接从database分析效率比较高
 */
class EnhancedSessionHandler extends CacheBasedSessionHandler
{
    const SESSION_STATICS = "session_statics";

    public function __construct($store, $minutes)
    {
		parent::__construct($store, $minutes);
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        $res = parent::open($savePath, $sessionName);
        event(new SessionOpenEvent($savePath, $sessionName));
        return $res;
    }
    /**
     * 创建Session时，如果发现用户从不同的IP访问，就记录[IP => 访问时间]，需要注意的是
     * 除非有写操作，否则该IP的访问时间是不会更新的。
     * 
     * session与登陆用户并没有直接关系，但是目前系统，只有登陆用户会创建session；
     * 而我们的需求也主要管理登陆用户，因此不记录关于匿名用户的信息；
     */
    protected function updateSession($sessionId, $session)
    {
        $ip = request()->ip();
        $userId = Auth::id(); //isset($session[Auth::getName()]) ? $session[Auth::id()] : -1;

        /* Log::info('session:' .  request()->ip() . ' , '   . $userId . ' ' . json_encode($session)); */
        try {
            if (!isset($session[self::SESSION_STATICS])) {
                $session[self::SESSION_STATICS] = ['ips' => []];
            }
            if ($userId) {
                // 保存该IP最近一次的访问时间
                $now = Carbon::now()->toIso8601String();
                if ($ip) {
                    $session[self::SESSION_STATICS]['ips'][$ip] = $now;
                }
                if (!isset($session[self::SESSION_STATICS]['email']))
                    $session[self::SESSION_STATICS]['email'] = Auth::user()->email;
                $session[self::SESSION_STATICS]['updated'] = $now;
            } else {
                unset($session[self::SESSION_STATICS]['ips'][$ip]);
                unset($session[self::SESSION_STATICS]['email']);
            }
        } catch(\Exception $e) {
        }
        // 存在并发问题，高频访问的数据不应该加锁，所以还是不能应该再维护一个表，而是动态扫描，然后保存结果
        /* $analytics = $this->cache->get(self::SESSION_ANALYTICS, []); */
        /* $analytics[$sessionId] = $userId; */
        /* $this->cache->put(self::SESSION_ANALYTICS, $analytics, $this->minutes); */

        return $session;
    }


    protected function formatedSessionId($sessionId)
    {
        $newSessionId = 'session.' . $sessionId;
        // TODO:下面这个判断主要是过于过渡，上线后一个月内将把该判断移除
        if ($this->cache->has($sessionId) && !$this->cache->has($newSessionId)) {
            // 下面这段是为了防止已经登陆的用户被踢出
            $this->cache->put($newSessionId, $this->cache->get($sessionId), $this->minutes);
            $this->cache->forget($sessionId);
        }
        return $newSessionId;
    }

    /**
     * @{inheritDoc}
     */
    public function read($sessionId)
    {
        return parent::read($this->formatedSessionId($sessionId));
    }

    /**
     * @{inheritDoc}
     */
    public function write($sessionId, $data)
    {
        $id = $this->formatedSessionId($sessionId);
        $session = $this->updateSession($id, @unserialize($data));
        if ($session)
            $data = serialize($session);
        $res = parent::write($id, $data);
        // 外部不需要知道session id已经被加工过
        event(new \App\Events\SessionWriteEvent($sessionId, $data));
        return ;
    }

    /**
     * @{inheritDoc}
     */
    public function destroy($sessionId)
    {
        $id = $this->formatedSessionId($sessionId);
        $res = parent::destroy($id);
        // 外部不需要知道session id已经被加工过
        event(new \App\Events\SessionDestroyEvent($sessionId));
        return $res;
    }

    public function gc($lifetime)
    {
        $res = parent::gc($lifetime);
        event(new \App\Events\SessionGcEvent($lifetime));
        return $res;
    }
}
