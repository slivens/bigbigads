<?php

use Illuminate\Database\Seeder;
use App\Affiliate;

class AffiliateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Affiliate::class, 3)->create();
        echo "affiliates filled\n";
    }
}
