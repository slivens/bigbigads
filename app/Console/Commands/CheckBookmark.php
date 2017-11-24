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
     * 每个用户只有1个名称为default的收藏夹,它的default值为1,用户自行创建的收藏夹，default值为0。
     * fix-all：检查所有用户，如果已有名为default的收藏夹，修正它使default值为1。如果没有，创建一个。
     * 检查所有用户，列出有2个或者以上收藏夹default值为1的用户
     *
     * @return mixed
     */
    public function handle()
    {
        $fixAll = $this->option('fix-all');

        $ok = $wrong = $created = $fix = 0; // 统计，赋初值
        $this->comment("checking bookmark start");
        User::chunk(1000, function ($users) use ($fixAll, $ok, $wrong, $created, $fix) {
            foreach ($users as $user) {
                $count = $user->bookmarks()->where('default', 1)->count(); // 用户正常的默认收藏夹数量
                if ($count > 1) {
                    $wrong++;
                    // 该用户有多于1个的默认收藏夹(default值为1)
                    $this->comment($user->email . ' has ' . $count . ' default bookmarks.');
                } elseif ($count <=1 && $fixAll) {
                    $oldBookmarkNum = Bookmark::where('uid', $user->id)->count(); //用户所有收藏夹
                    // 该用户只有1个默认收藏夹或者没有默认收藏夹，并且指令要求修复
                    Bookmark::updateOrCreate(
                        [
                            'uid' => $user->id,
                            'name' => Bookmark::DEFAULT
                        ],
                        [
                            'default' => 1
                        ]
                    );
                    // 修改完毕之后检查一下
                    // 如原收藏夹数量不正确(0个)，修改完毕之后有1个
                    if ($count == 0 && Bookmark::where('uid', $user->id)->where('default', 1)->count() == 1) {
                        if ($oldBookmarkNum == 0) {
                            // 用户一个收藏夹都没有
                            $created++;
                            $this->comment("$user->email's default bookmark was added");
                        } else {
                            // 有收藏夹
                            $fix++;
                            $this->comment("$user->email's default bookmark fix upon");
                        }
                    }
                } elseif ($count == 0) {
                    // 默认收藏夹不正确但是不要求修复
                    $wrong++;
                }
            }
            $userCount = count($users);
            $ok = $userCount - $wrong - $created - $fix;
            $this->comment("$userCount users, $ok ok, $created created, $fix fix.");
        });
        $this->comment("checking bookmark end");
    }
}
