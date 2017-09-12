<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Role;

class CheckUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:check-usage {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查权限是否符合预期设计';

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
        $email = $this->argument('email');

        $this->comment("checking role cache usage...");
        try {
            foreach (Role::where('id', '>', 2)->get() as $role) {
                $role->checkCacheUsage();
            }
        } catch(\App\Exceptions\GenericException $e) {
            $this->error($e->getMessage());
        }
    }
}
