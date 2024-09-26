<?php

namespace App\Models;


class FacilityTag extends BaseModel
{
    protected $table = 'facility_tag';
    
    public $facility_id;
    public $tag_id;
}