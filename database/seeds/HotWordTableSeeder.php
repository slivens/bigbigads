<?php

use Illuminate\Database\Seeder;
use App\HotWord;

class HotWordTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $hotWord = [
                [
                    "keyword" => "Insurance",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "realtor",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "loan",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "spinner",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "free shipping",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "shoe",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "cox",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "mac pro",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "fashion",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "Forex Global",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "Philips Avent",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "Center Sphere",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "Khalsa Brain Games",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "The Five Dollar Movie",
                    "type" => "",
                    "status" => "1",
                ],
                [
                    "keyword" => "Gizmos&gadgets",
                    "type" => "",
                    "status" => "1",
                ],
            ];

            //清除热词再填充
            HotWord::where('id', '>', 0)->delete();
            foreach($hotWord as $key=>$item) {
                HotWord::create($item);
            }
            echo "insert hot word\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

    }
}
