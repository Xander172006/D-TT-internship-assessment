<?php

namespace App\Factory;

use App\Models\Tag;
use Faker\Factory as FakerFactory;

class TagFactory extends BaseFactory {
    protected $model = Tag::class;

    protected function defaultAttributes() {
        $faker = FakerFactory::create();

        return [
            // create unique names
            'name' => join('-', $faker->unique()->words()),
        ];
    }
}
