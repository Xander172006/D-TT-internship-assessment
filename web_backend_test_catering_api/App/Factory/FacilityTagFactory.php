<?php

namespace App\Factory;

use App\Models\FacilityTag;
use App\Models\Facility;
use App\Models\Tag;

class FacilityTagFactory extends BaseFactory {
    protected $model = FacilityTag::class;
    public static $previousIds = [];

    protected function defaultAttributes() {
        $data = [
            'facility_id' => (new Facility())->inRandomOrder()->first()->id,
            'tag_id' => (new Tag())->inRandomOrder()->last()->id,
        ];
        
        while(in_array(join(',', array_values($data)), self::$previousIds)) {
            $data['facility_id'] = (new Facility())->inRandomOrder()->first()->id;
            $data['tag_id'] = (new Tag())->inRandomOrder()->last()->id;
        }

        return $data;
    }
}
