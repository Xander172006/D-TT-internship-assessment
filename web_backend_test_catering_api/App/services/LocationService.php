<?php

namespace App\Services;

use App\Plugins\Di\Injectable;
use App\Repository\LocationRepository;

class LocationService extends Injectable {
    public LocationRepository $locationRepository;

    public function __construct()
    {
        $this->locationRepository = new LocationRepository();
    }

    public function create(string $name, $address, $zip_code, $country_code, $phone_number): void
    {
        $this->locationRepository->create($name, $address, $zip_code, $country_code, $phone_number);
    }
}