<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Carbon\Carbon;
use Log;

class ChangeTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:tag {user_email : 用户邮箱，bba邮箱} {tag? : 要设置的tag值，不填为default，可选white/black}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '改变用户的tag值,默认default,可选值有white和black，白名单和黑名单，白名单现阶段可用，黑名单备用';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('user_email');
        $tag = $this->argument('tag');
        
        if($tag == 'white' || strtolower($tag) == 'whilelist') {
            $tag = User::TAG_WHITELIST;
        } elseif($tag == 'black' || strtolower($tag) == 'blacklist') {
            $tag = User::TAG_BLACKLIST;
        } else {
            $tag = User::TAG_DEFAULT;
        }
        $user = User::where('email',$email)->first();
        if(!$user) {
            $this->comment('cannot find user with '.$email);
            return;
        } else {
            $user->tag = $tag;
            $user->save();
            $now = Carbon::now()->toDateTimeString();
            $re_str = "user (name: $user->name ,email: $email) has change tag to $tag,change time is $now";
            log::info($re_str);
            $this->comment($re_str);
        }
    }
}
