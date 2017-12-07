<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(DataTypesTableSeeder::class);
        $this->call(DataRowsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
        /* $this->call(SettingsTableSeeder::class); */
        /* $this->call(PermissionsTableSeeder::class); */
        /* $this->call(PermissionRoleTableSeeder::class); */
        /* $this->call(PoliciesTableSeeder::class); */
        /* $this->call(PolicyRoleTableSeeder::class); */
        $this->call(RolesTableSeeder::class);
        $this->call(PolicyUserTableSeeder::class);
    }
}
