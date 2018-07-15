<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Promotion::class, function (Faker $faker) {
    $now =Carbon::now('Asia/Ho_Chi_Minh');
    $days = rand(3,200);
    return [
        'name' => $faker->unique()->streetName,
        'description' => $faker->realText($maxNbChars = 200, $indexSize = 2),
        'started_date' => $faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
        'ended_date' => $faker->dateTimeInInterval($startDate = 'now', $interval = '+ '.$days.' days', $timezone = null),
        'actived' => $faker->randomElement($array = array (true,false)),
        'disposabled' => $faker->randomElement($array = array (true,false)),
    ];
});
