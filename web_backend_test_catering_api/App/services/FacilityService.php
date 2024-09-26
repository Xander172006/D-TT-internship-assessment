<?php

namespace App\Services;

use App\Plugins\Di\Injectable;
use App\Repository\FacilityRepository;

class FacilityService extends Injectable {
    public FacilityRepository $facilityRepository;

    public function __construct()
    {
        $this->locationRepository = new FacilityRepository();
    }

    public function create(string $name, $creation_date, $location_id, $tag_id): void
    {
        $this->locationRepository->create($name, $creation_date, $location_id, $tag_id);
    }
}