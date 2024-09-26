<?php

namespace App\Repository;

use App\Models\Facility;
use App\Models\Location;
use App\Models\Tag;


use App\Plugins\Di\Injectable;

class FacilityRepository extends Injectable {

    public function create(string $name, int $location_id, int $tag_id): Facility
    {
        return Facility::query()->create([
            'name' => $name,
            'creation_date' => $this->date->format('Y-m-d H:i:s'),
            'location_id' => $location_id,
            'tag_id' => $tag_id,
        ]);
    }
}