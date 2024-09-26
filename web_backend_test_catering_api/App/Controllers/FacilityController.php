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
    protected $response;
    
   /**
     * Retrieve all facilities along with their locations and tags.
     *
     * @return void
     */
    public function index() {
        $facilities = Facility::query()->get();
    
        foreach ($facilities as $facility) {
            // Retrieve the location and tags for each facility
            $facility->location = Location::query()->findById($facility->location_id);
            $facility->tags = [];
            $facilityTags = FacilityTag::query()->where('facility_id', '=', $facility->id)->get();

            // retrieve one or more tags
            foreach ($facilityTags as $facilityTag) {
                $tag = Tag::query()->findById($facilityTag->tag_id);
                if ($tag) {
                    $facility->tags[] = $tag;
                }
            }
        }
    
        $response = [
            'facilities' => $facilities,
            'locations' => [],
            'tags' => []
        ];
    
        (new Status\Ok($response))->send();
    }

    /**
     * Display a specific facility by ID along with its location and tags.
     *
     * @param string $id The ID of the facility.
     * @return void
     */
    public function show(string $id) {
        $facility = Facility::query()->findById($id);
        
        if ($facility === null) {
            (new Status\BadRequest())->send();
            return;
        }
    
        // Retrieve the location and tags for the facility
        $facility->location = Location::query()->findById($facility->location_id);
        $facilityTags = FacilityTag::query()->where('facility_id', '=', $id)->get();
        $tags = [];
    
        // retrieve one or more tags
        foreach ($facilityTags as $facilityTag) {
            $tag = Tag::query()->findById($facilityTag->tag_id);
            if ($tag) {
                $tags[] = $tag;
            }
        }
    
        $facility->tags = $tags;
        $response = [
            'facility' => [
                'id' => $facility->id,
                'name' => $facility->name,
                'creation_date' => $facility->creation_date,
                'location' => $facility->location,
                // Filter out empty tags
                'tags' => array_filter($facility->tags, function($tag) {
                    return !empty($tag->name);
                }),
            ]
        ];
        
        (new Status\Ok($response))->send();
    }
    
    /**
     * Create a new facility with associated location and tags.
     *
     * @return void
     */
    public function create() {
        $name = $this->request->get('name');
        $creation_date = $this->request->get('creation_date');
        $location_id = $this->request->get('location_id');
        $tag_id = $this->request->get('tag_id');
        $tagname = $this->request->get('tag');

        if (empty($name) || empty($creation_date) || empty($location_id) || empty($tag_id)) {
            (new Status\BadRequest(["error" => "Bad request!"]))->send();
            return;
        }

        // validate if the location exists
        $location = Location::query()->findById($location_id);
        if ($location === null) {
            (new Status\BadRequest(["error" => "Location not found!"]))->send();
            return;
        }

        // validate if the tag exists, if not create a new one
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

    /**
     * Update an existing facility along with its associated tags.
     *
     * @param string $id The ID of the facility to be updated.
     * @return void
     */
    public function update(string $id) {
        $name = $this->request->get('name');
        $creation_date = $this->request->get('creation_date');
        $location_id = $this->request->get('location_id'); 
        $tags = $this->request->get('tags'); 

        if (empty($name) || empty($creation_date) || empty($location_id) || empty($tags)) {
            (new Status\BadRequest(["error" => "Bad request!"]))->send();
            return;
        }

        // validate if location exists
        $location = Location::query()->findById($location_id);
        if ($location === null) {
            (new Status\BadRequest(["error" => "Location not found!"]))->send();
            return;
        }

        // validate if facility already exists
        $facility = Facility::query()->findById($id);
        if ($facility === null) {
            (new Status\BadRequest(["error" => "Facility not found!"]))->send();
            return;
        }

        // validate if tags is an array
        $newTags = [];
        $tags = json_decode($tags, true);
        if (!is_array($tags)) {
            (new Status\BadRequest(["error" => "Tags must be an array!"]))->send();
            return;
        }
        
        foreach ($tags as $tag) {
            $tag = Tag::query()->create([
                'name' => $tag,
            ]);
            $newTags[] = $tag;
        }
        
        $response = [
            'facility' => $facility,
            'location' => $location,
            'tags' => $newTags,
        ];
    
        (new Status\Ok($response))->send();
    }

    /**
     * Delete a facility by ID.
     *
     * @param string $id The ID of the facility to be deleted.
     * @return void
     */
    public function delete(string $id) {
        $facility = Facility::query()->findById($id);
        if ($facility === null) {
            (new Status\BadRequest(["error" => "Facility not found!"]))->send();
            return;
        }
    
        $facility->delete();
        
        (new Status\Ok(["message" => "Facility deleted successfully!"]))->send();
    }    
}
