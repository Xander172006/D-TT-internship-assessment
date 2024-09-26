<?php

namespace App\Repository;

use App\Models\Location;
use App\Plugins\Di\Injectable;

class LocationRepository extends Injectable {

    public function create(string $city, string $address, string $zip_code, string $country_code, string $phone_number): Location
    {
        return Location::query()->create([
            'city' => $city,
            'address' => $address,
            'zip_code' => $zip_code,
            'country_code' => $country_code,
            'phone_number' => $phone_number,
        ]);
    }


}