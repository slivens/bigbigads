<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface SessionService {

    /**
     * @return 返回所有在线会话, 格式:
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
    public function sessionInfos() : Collection;

    /**
     * 根据session id获取指定session
     * @return 获取用户的Session
     */
    public function session($sessionId);


    /**
     * @return 返回所有用户，格式:
     * ```
     * [
     *  ['email1' => [$session1, $session2]],
     *  ['email2' => [$session3, $session4]],
     * ]
     * ```
     * 其中$session1与`sessions()`接口返回的数据项格式一样
     */
    public function userInfos() : Collection;

    /**
     * 删除指定session id的Session
     * @param $sessionId
     */
    public function removeSession(string $sessionId);

    /**
     * 删除用户的会话，可指定保留session的数量。删除规则是将session的updated最早的将先被删除
     * @param string $email 用户email
     * @param int $reserved 保留的session数量
     */
    public function removeUserSessions(string $email, int $reserved = 0);
}
