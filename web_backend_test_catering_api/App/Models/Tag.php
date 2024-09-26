<?php

namespace App\Models;

class Tag extends BaseModel
{
    protected $table = 'tag';
    protected $primaryKey = 'id';

    public $id;
    public $name;

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'facility_tag', 'tag_id', 'facility_id');
    }
}
