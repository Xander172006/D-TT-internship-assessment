<?php

namespace App\Models;

class Location extends BaseModel
{
    protected $table = 'location';
    protected $primaryKey = 'id';

    public $id;
    public $city;
    public $address;
    public $zip_code;
    public $country_code;
    public $phone_number;
}
