<?php

use Illuminate\Database\Seeder;
use App\Promotion;

class PromotionCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\PromotionCode::class, Promotion::inRandomOrder()->first()->amount)->create();
    }
}
