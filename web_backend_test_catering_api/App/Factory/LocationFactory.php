<?php

namespace App\Factory;

use App\Models\Location;
use Faker\Factory as FakerFactory;

class LocationFactory extends BaseFactory {
    protected $model = Location::class;

    protected function defaultAttributes() {
        $faker = FakerFactory::create();

        return [
            'city' => $faker->city(),
            'address' => $faker->address(),
            'zip_code' => $faker->postcode(),
            'country_code' => $faker->countryCode(),
            'phone_number' => $faker->phoneNumber(),
        ];
    }
}
