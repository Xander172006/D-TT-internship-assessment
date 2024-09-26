<?php

namespace App\Controllers;

use App\Models\Facility;
use App\Models\Location;
use App\Models\Tag;
use App\Models\FacilityTag;

use App\Repository\FacilityRepository;
use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;

class FacilityController extends BaseController {

    public function index() {
        $facilities = Facility::query()->get();
    
        // Get every corresponding location by using the facility(location_id) for each facility
        foreach ($facilities as $facility) {
            $facility->location = Location::query()->findById($facility->location_id);
            
            // Initialize tags for the facility
            $facility->tags = []; // Ensure tags is initialized
    
            // Get tags for this facility
            $facilityTags = FacilityTag::query()->where('facility_id', '=', $facility->id)->get();
            foreach ($facilityTags as $facilityTag) {
                // Find tag by its ID and add to facility's tags
                $tag = Tag::query()->findById($facilityTag->tag_id);
                if ($tag) {
                    $facility->tags[] = $tag; // Collecting tag objects into an array
                }
            }
        }
    
        $response = [
            'facilities' => $facilities,
            'locations' => [], // Since locations can be derived from facilities, may not need to be a separate field
            'tags' => [] // This should be omitted or structured better
        ];
    
        (new Status\Ok($response))->send();
    }



    public function show(string $id) {
        $facility = Facility::query()->findById($id);

        if ($facility === null) {
            (new Status\BadRequest())->send();
            return;
        }

        // return the corresponding location by using the facility(location_id)
        $facility->location = Location::query()->findById($facility->location_id);


        // return the corresponding tags by using the facility(tag_id). A facility can have multiple tags
        $tags = FacilityTag::query()->where('facility_id', '=', $id)->get();

        foreach ($tags as $tag) {
            $tagIds[] = $tag->tag_id; // Collecting tag_ids into an array
        }

        $facility->tags = Tag::query()->findbyId($facility->tag_id);


        $response = [
            'facility' => $facility,
            'location' => $facility->location,
            'tags' => $facility->tags,
        ];

        (new Status\Ok($response))->send();
    }




    public function create() {
        $name = $this->request->get('name');
        $creation_date = $this->request->get('creation_date');
        $location_id = $this->request->get('location_id');
        $tag_id = $this->request->get('tag_id');
        $tagname = $this->request->get('tag');

        // check if request body is empty
        if (empty($name) || empty($creation_date) || empty($location_id) || empty($tag_id)) {
            (new Status\BadRequest(["error" => "Bad request!"]))->send();
            return;
        }

        // check if location and tag exists
        $location = Location::query()->findById($location_id);
        if ($location === null) {
            (new Status\BadRequest(["error" => "Location not found!"]))->send();
            return;
        }

        $tag = Tag::query()->findById($tag_id);
        if ($tag === null) {
            if ($tagname) {
                $tag = Tag::query()->create([
                    'name' => $tagname,
                ]);
                $tag_id = $tag->id;
            } else {
                (new Status\BadRequest(["error" => "Tag not found!"]))->send();
                return;
            }
        }
        
        $facility = Facility::query()->create([
            'name' => $name,
            'creation_date' => $creation_date,
            'location_id' => $location_id,
            'tag_id' => $tag_id,
        ]);

        (new Status\Ok($facility))->send();
    }
}