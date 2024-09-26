<?php

namespace App\Factory;

use App\Models\Facility;
use App\Models\Location;
use App\Models\Tag;
use Faker\Factory as FakerFactory;

class FacilityFactory extends BaseFactory {
    protected $model = Facility::class;

    protected function defaultAttributes(array $attributes = []): array {
        $faker = FakerFactory::create();
        return [
            'name' => $faker->company(),
            'creation_date' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            'location_id' => (new Location())->inRandomOrder()->first()->id,
        ];
    }
}
