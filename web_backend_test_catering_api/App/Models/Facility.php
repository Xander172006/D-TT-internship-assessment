<?php

namespace App\Models;

class Facility extends BaseModel
{
    protected $table = 'facility';
    protected $primaryKey = 'id';
    
    public $id;
    public $name;
    public $creation_date;
    public $location_id;
    
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'facility_tag', 'facility_id', 'tag_id');
    }
}