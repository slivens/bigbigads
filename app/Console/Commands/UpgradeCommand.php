<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class UpgradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:upgrade {--generate : 生成升级相关的iseed文件} {--recovery : 从iseed文件中升级系统} {--menus : 从seed文件升级菜单}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '升级命令：数据库的迁移';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function seed($class)
    {
        $instance = new $class;
        $instance->run();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('menus')) {
            if ($this->option('recovery')) {
                $this->seed(\DataTypesTableSeeder::class);
                $this->seed(\DataRowsTableSeeder::class);
                $this->seed(\MenusTableSeeder::class);
                $this->seed(\MenuItemsTableSeeder::class);
                $this->comment('menu recoveried');
            } else {
                $this->call('iseed', [
                    'tables' => 'data_types,data_rows,menus,menu_items',
                    '--force' => true
                ]);
            }
        } else if ($this->option('generate')) {
            $this->call('iseed', [
                'tables' => 'roles,permissions,permission_role,policies,policy_role,policy_user,data_types,data_rows,menus,menu_items',
                '--force' => true
            ]);
        } else if ($this->option('recovery')) {
            // 临时禁止外键以便重建表，高危操作，只有明确自己操作的影响才能使用该功能
            try {
                DB::beginTransaction();
                \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
                $this->seed(\RolesTableSeeder::class);
                \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

                $this->call('iseed', [
                    'tables' => 'policy_user',
                    '--force' => true
                ]);
                $this->seed(\PermissionsTableSeeder::class);
                $this->seed(\PermissionRoleTableSeeder::class);
                $this->seed(\PoliciesTableSeeder::class);
                $this->seed(\PolicyRoleTableSeeder::class);
                $this->seed(\PolicyUserTableSeeder::class);
                DB::commit();
                $this->comment("completed");
            } catch(\Exception $e) {
                DB::rollBack();
                $this->comment("something goes wrong, rollback");
                throw $e;
            }
        } else {
            $this->error("no operation");
        }
    }
}
