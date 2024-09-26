<?php

namespace App\Controllers;

use App\Models\Location;
use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;

class LocationController extends BaseController {

    /**
     * Controller function used to test whether the project was set up properly.
     * @return void
     */
    public function index() {
        $locations = Location::query()->get();
        (new Status\Ok($locations))->send();
    }


    public function show(string $id) {
        $location = Location::query()->findById($id);
        if ($location === null) {
            (new Status\BadRequest())->send();
            return;
        }
        (new Status\Ok($location))->send();
    }


    public function create() {
        $city = $this->request->get('city');
        $address = $this->request->get('address');
        $zip_code = $this->request->get('zip_code');
        $country_code = $this->request->get('country_code');
        $phone_number = $this->request->get('phone_number');

        if (empty($city) || empty($address) || empty($zip_code) || empty($country_code) || empty($phone_number)) {
            (new Status\BadRequest(["error" => "Bad request!"]))->send();
            return;
        }

        $location = Location::query()->create([
            'city' => $city,
            'address' => $address,
            'zip_code' => $zip_code,
            'country_code' => $country_code,
            'phone_number' => $phone_number,
        ]);

        (new Status\Ok($location))->send();
    }
}
