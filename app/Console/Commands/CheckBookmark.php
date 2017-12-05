<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Bookmark;

class CheckBookmark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:check-bookmark {--fix-all : 修复用户默认收藏夹,一个用户只有1个}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置所有用户默认收藏夹;检查所有用户,列出收藏夹异常的用户。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * 每个用户只有1个默认收藏夹,它的default值为1,用户自行创建的收藏夹，default值为0。
     * fix-all：检查所有用户，如果已有默认收藏夹，修正它使default值为1。如果没有，创建一个。
     * 检查所有用户，列出有2个或者以上收藏夹default值为1的用户,只检查不处理
     *
     * @return mixed
     */
    public function handle()
    {
        $fixAll = $this->option('fix-all');

        $ok = $wrong = $created = $fix = 0; // 统计，赋初值
        $this->comment("checking bookmark start.");
        User::chunk(1000, function ($users) use ($fixAll, $ok, $wrong, $created, $fix) {
            foreach ($users as $user) {
                $defaultBookmarks = $user->bookmarks()->where('default', 1)->get();
                $count = count($defaultBookmarks); // 用户正常的默认收藏夹数量
                if ($count > 1) {
                    $wrong++;
                    // 该用户有多于1个的默认收藏夹(default值为1)
                    $this->comment($user->email . ' has ' . $count . ' default bookmarks.');
                } elseif ($count <= 1 && $fixAll) {
                    // 该用户只有1个默认收藏夹或者没有默认收藏夹，并且指令要求修复
                    $oldBookmarkNum = $user->bookmarks()->count(); // 用户所有收藏夹
                    // 如果用户权限为free,那么只有1个收藏夹，直接修改名称和default值
                    if ($user->role_id == 3 && $count == 0 && $bookmark = $user->bookmarks()->first()) {
                        $bookmark->name = Bookmark::DEFAULT;
                        $bookmark->default = 1;
                        $bookmark->save();

                        $fix++;
                        $this->comment("$user->email is free user,change bookmark name and default value.");
                    } else {
                        // 非free用户 或者免费用户有默认收藏夹
                        // 分情况处理：有默认收藏夹时检查名称，没有时直接创建一个新收藏夹，允许超限。
                        if ($count == 0) {
                            // 没有默认收藏夹，创建。
                            $newBookmark = new Bookmark;
                            $newBookmark->uid = $user->id;
                            $newBookmark->name = Bookmark::DEFAULT;
                            $newBookmark->default = 1;
                            $newBookmark->save();

                            $created++;
                            $this->comment("$user->email's default bookmark was added.");
                        } else {
                            // 有默认收藏夹，如果名称不对，修改。
                            // 免费用户适用
                            $defaultBookmark = $defaultBookmarks[0];
                            if ($defaultBookmark->name != Bookmark::DEFAULT) {
                                $defaultBookmark->name = Bookmark::DEFAULT;
                                $defaultBookmark->save();

                                $fix++;
                                $this->comment("$user->email's default bookmark name was wrong, fix it.");
                            }
                        }
                    }
                } elseif ($count == 0) {
                    // 默认收藏夹不正确但是不要求修复
                    $wrong++;
                }
                // 额外操作，可能不必要
                // 默认收藏夹的名称有唯一性要求，在表里面不是。如果有异常（比如所有名称都跟默认收藏夹一致），重命名，给名称加上编号。
                // 该修复操作中会出现非默认收藏夹名称与默认收藏夹一致的原因只有：持有数到上限+有1个或多个文件夹名称与默认一致
                // 暂不使用
                /* if ($likeBookmarksNum = $user->bookmarks()->where('name', Bookmark::DEFAULT)->where('default', 0)->count()) {
                    $this->comment("$user->email's has $likeBookmarksNum bookmark name was the same as default bookmarks.");
                    $wrong++;
                } */
            }
            $userCount = count($users);
            $ok = $userCount - $wrong - $created - $fix;
            $this->comment("$userCount users, $ok ok, $created created, $fix fix.");
        });
        $this->comment("checking bookmark end.");
    }
}
