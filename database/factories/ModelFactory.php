<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */


$factory->define(\LeaseTracker\Vehicle::class, function(Faker\Generator $faker) {
    return [
       'name' => $faker->name,
        'make_model' => $faker->text,
        'cost_per_mile' => $faker->randomFloat(2, 0, 5),
        'total_allowable_mileage' => $faker->numberBetween(0, 50000),
        'months' => $faker->numberBetween(1, 60),
        'starting_mileage' => $faker->numberBetween(0, 500),
        'start_date' => $faker->date('Y-m-d'),
        'image_url' => $faker->url,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});

$factory->define(LeaseTracker\MileEntry::class, function(Faker\Generator $faker) {
    return [
        'carId' => $faker->numberBetween(0,10),
        'date' => $faker->date('Y-m-d'),
        'currentMileage' => $faker->numberBetween(1,50000),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime
    ];
});