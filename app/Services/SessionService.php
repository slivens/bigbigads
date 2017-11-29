<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Carbon\Carbon;
use DB;
use Log;
use App\User;

/**
 * 会话管理
 *
 * 获取当前所有在线用户
 * 获取所有会话
 */
class SessionService implements \App\Contracts\SessionService
{
    const REDIS_SESSION_PREFIX = 'laravel:session.';
    private $sessionInfo;
    private $userInfo;

    public function __construct()
    {
    }

    protected function getDbStore()
    {
        return app('session')->driver('database')->getHandler();
    }

    protected function getStore()
    {
        return app('session.store')->getHandler();
    }

    /**
     * Get a fresh query builder instance for the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getQuery()
    {
        return DB::table('sessions');
    }

    /**
     * 获取过滤后的sessions
     */
    private function getFilteredSessions($where = null)
    {
        $result = new Collection();
        $query = $this->getQuery()->whereNotNull('user_id');
        if ($where)
            $query->where($where);
        $query->chunk(100, function($rows) use($result) {
            foreach ($rows as $row) {
                $session = $this->getDbStore()->read($row->id);

                if (!$session) 
                    continue;
                $session = @unserialize($session);

                if (!isset($session['session_statics']))
                    continue;
                if (!isset($session['session_statics']['email']))
                    continue;
                $result[$row->id] = $session['session_statics'];    
            }
        });
        return $result;
    }

    /**
     * @{inheritDoc}
     */
    public function sessionInfos(bool $forced = false) : Collection
    {
        if (!$forced && $this->sessionInfo)
            return $this->sessionInfo;

        $this->sessionInfo = $this->getFilteredSessions();
        return $this->sessionInfo;
    }

    /**
     * @{inheritDoc}
     */
    public function session(string $sessionId) : ?array
    {
        $session = $this->getStore()->read($sessionId);
        if (!$session)
            return null;
        // Session->Handler->Cache，序列化了2次，这里通过handler获取了session, 还需要反序列化1次才得到真正的Session
        return @unserialize($session);
    }

    /**
     * @{inheritDoc}
     */
    public function userInfos(bool $forced = false) : Collection
    {
        if (!$forced && $this->userInfo)
            return $this->userInfo;
        $sessionInfo = $this->sessionInfos($forced);
        $infos = new Collection();
        foreach ($sessionInfo as $key => $statics) {
            $subSet = $infos->get($statics['email'], []);
            $subSet[$key] = $statics;
            $infos[$statics['email']] = $subSet;
        }
        $this->userInfo = $infos;
        return $infos;
    }

    /**
     * @{inheritDoc}
     */
    public function userSessions(string $email) : Collection
    {
        $user = User::where('email', $email)->first();
        if (!$user)
            return new Collection();
        return $this->getFilteredSessions(['user_id' => $user->id]);
    }

    /**
     * @{inheritDoc}
     */
    public function removeSession(string $sessionId) : void
    {
        /* Log::debug("destroy:" . $sessionId); */
        $this->getStore()->destroy($sessionId);
    }

    /**
     * @{inheritDoc}
     */
    public function removeUserSessions(string $email, int $reserved = 0) : int
    {
        if ($reserved < 0)
            return -1;
        $sessions = $this->userSessions($email);
        if (count($sessions) < $reserved) {
            return -1;
        }
        $sorted = Arr::sort($sessions, function($val, $key) {
            return new Carbon($val['updated']);
        });
        $keys = array_keys($sorted);
        $delCount = count($keys) - $reserved;
        for ($i = 0; $i < min(count($keys), $delCount); ++$i) {
            $this->removeSession($keys[$i]);
        }
        return min($reserved, count($keys));
    }

}
