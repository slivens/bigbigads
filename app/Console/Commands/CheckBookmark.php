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
    protected $signature = 'bba:checkBookmark {--fix-all : 修复用户默认收藏夹,一个用户只有1个} {--check-all : 检查所有用户,列出收藏夹异常账户}';

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
     * 每个用户只有1个名称为default的收藏夹。
     * fix-all：检查所有用户，如果已有名为default的收藏夹，修正它使default值为1。如果没有，创建一个。
     * check-all:检查所有用户，列出有2个或者以上default值为1的用户
     * 如果不带参数，就都检查
     *
     * @return mixed
     */
    public function handle()
    {
        $fixAll = $this->option('fix-all');
        $checkAll = $this->option('check-all');
        if (!$fixAll && !$checkAll) {
            $fixAll = true;
            $checkAll = true;
        }

        $ok = $wrong = $created = $fix = 0; // 统计，赋初值
        $this->comment("checking bookmark start");
        foreach (User::all() as $user) {
            $count = Bookmark::where('uid', $user->id)->where('default', 1)->count(); // 用户正常的收藏夹数量
            if ($count > 1 && $checkAll) {
                $wrong++;
                // 该用户有多于1个的默认收藏夹(default值为1),并且指令要求输出
                $this->comment($user->email . ' has ' . $count . ' default bookmarks.');
            } elseif ($count <=1 && $fixAll) {
                $oldBookmarkNum = Bookmark::where('uid', $user->id)->count(); //用户所有收藏夹
                // 该用户只有1个默认收藏夹或者没有默认收藏夹，并且指令要求修复
                Bookmark::updateOrCreate(
                    [
                        'uid' => $user->id,
                        'name' => 'default'
                    ],
                    [
                        'default' => '1'
                    ]
                );
                // 修改完毕之后检查一下
                // 原收藏夹数量不正确(0个)，修改完毕之后有1个
                if ($count == 0 && Bookmark::where('uid', $user->id)->where('default', 1)->count() == 1) {
                    if ($oldBookmarkNum == 0) {
                        // 用户一个收藏夹都没有
                        $created++;
                    } else {
                        // 有收藏夹
                        $fix++;
                    }
                    $this->comment("$user->email's default bookmark fix upon");
                }
            }
        }
        $userCount = User::count();
        $ok =$userCount - $wrong - $created - $fix;
        $this->comment("checking bookmark end");
        $this->comment("$userCount users, $ok ok, $created created, $fix fix.");
    }
}
