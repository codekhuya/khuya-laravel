<?php

use Faker\Generator as Faker;
use App\Promotion;

$factory->define(App\PromotionCode::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->firstNameMale),
        'actived' => $faker->randomElement($array = array (true,false)),
        'value' => rand(1,50),
        'type' => $faker->randomElement($array = array ('percent','money')),
        'promotion_id' => Promotion::inRandomOrder()->first()->id,
    ];
});
