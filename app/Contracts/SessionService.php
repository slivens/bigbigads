<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface SessionService {

    /**
     * @return Illuminate\Support\Collection 返回所有会话格式:
     * ```
     * [
     *  'session1_id' =>[
     *       'user1_email',
     *       'ips',
     *       'updated'
     *  ]
     * ]
     * ```
     */
    public function sessionInfos(bool $forced = false) : Collection;

    /**
     * 根据session id获取指定session
     * @string string $sessionId 从`sessionInfos`中获取到的sessionId
     * @return array 获取用户的Session
     */
    public function session(string $sessionId) : ?array;


    /**
     * @return Illuminate\Support\Collection 返回用户的会话格式:
     * ```
     * [
     *  ['email1' => [$session1, $session2]],
     *  ['email2' => [$session3, $session4]],
     * ]
     * ```
     * 其中$session1与`sessionInfos()`接口返回的数据项格式一样
     *

     */
    public function userInfos(bool $forced = false) : Collection;

    /**
     * 删除指定session id的Session
     * @param $sessionId
     * @return void 无
     */
    public function removeSession(string $sessionId) : void;

    /**
     * 删除用户的会话，可指定保留session的数量。删除规则是将session的updated最早的将先被删除
     * @param string $email 用户email
     * @param int $reserved 保留的session数量
     * @return 剩余的session数量
     */
    public function removeUserSessions(string $email, int $reserved = 0) : int;
}
