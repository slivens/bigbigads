<?php

namespace App\Listeners;

use App\Events\SessionOpenEvent;
use App\Events\SessionWriteEvent;
use App\Events\SessionDestroyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Session\DatabaseSessionHandler;
use Log;
use Voyager;
use Request;
use DB;

/**
 * 每次用户发送请求都会更新过期时间
 * 所以对于数据库的写入是极为频繁的，虽然放在队列中并不会影响到实际运行性能
 * 但是会影响磁盘寿命和队列的执行效率，后续还是会考虑再跟redis做一轮比较，目前是redis的扫描性能并不如数据库的索引效率高
 */
class SessionEventSubscriber  implements ShouldQueue
{
    use InteractsWithQueue;
    private $sessionService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    protected function getStore()
    {
        // 使用队列有一个问题,就是容器对象不会释放
        // 因此，如果使用app('session')->driver
        // 生成的SessionHandler将保存上一次执行时的状态
        // 比如$this->exists属性总为true不会变，于是写入时就无法正常插入，总是update
        // 写入就出问题了，因此这里手动创建DabaseSessionHandler
        $connection = app()['db']->connection(null);
        $table = app()['config']['session.table'];
        $lifetime = app()['config']['session.lifetime'];

        return new DatabaseSessionHandler($connection, $table, $lifetime, app());
        /* return app('session')->driver('database')->getHandler(); */
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

    /* public function onSessionOpen($event) */
    /* { */
    /*     Log::debug("on session start"); */
    /* } */

    /**
     * Session有写入时检查
     */
    public function onSessionWrite($event)
    {
        // 事件的处理使用了队列机制，而队列是上下文无关的
        // 这意味着登陆信息和Request是无法直接获取的。
        // 但是Database的Session Handler会去引用上下文
        // 因此我们需要将信息传递给$event，并从$event中获取加工处理
        // 使用$this->getStore()获取系统的句柄可以极大地简化我们的工作
        // 并不意味着它是最佳方案。这里将会有两次写数据库操作，不过
        // 是在队列中并不影响
        $this->getStore()->write($event->sessionId, $event->session);
        // 重写上下文
        $payload = $event->payload;
        /* Log::debug($event->sessionId, $payload); */
        $this->getQuery()->where('id', $event->sessionId)->update($payload);
    }

    public function onSessionDestroy($event)
    {
        /* Log::debug("on session destroy"); */
        $this->getStore()->destroy($event->sessionId);
    }

    public function onSessionGc($event)
    {
        /* Log::debug("on session gc"); */
        $this->getStore()->gc($event->lifetime);
    }

    public function subscribe($events)
    {
        /* $events->listen( */
        /*     SessionOpenEvent::class, */
        /*     self::class . '@onSessionOpen' */
        /* ); */

        $events->listen(
            SessionWriteEvent::class,
            self::class . '@onSessionWrite'
        );

        $events->listen(
            SessionDestroyEvent::class,
            self::class . '@onSessionDestroy'
        );

        $events->listen(
            SessionGcEvent::class,
            self::class . '@onSessionGc'
        );
    }
}
