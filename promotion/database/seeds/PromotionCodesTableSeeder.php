<?php

use Illuminate\Database\Seeder;

class PromotionCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\PromotionCode::class, 300)->create();
    }
}
