<?php

namespace App\Repository;

use App\Models\Facility;
use App\Models\Location;
use App\Models\Tag;
use App\Models\FacilityTag;


use App\Plugins\Di\Injectable;
use App\Plugins\Http\Response as Status;

class FacilityRepository extends Injectable 
{
    protected $tags = [];

    public function create(string $name, int $location_id, int $tag_id): Facility
    {
        return Facility::query()->create([
            'name' => $name,
            'creation_date' => $this->date->format('Y-m-d H:i:s'),
            'location_id' => $location_id,
            'tag_id' => $tag_id,
        ]);
    }

     /**
     * Search for facilities by name, tag, or location city.
     *
     * @param array $queryParams
     * @return void
     */
    public function search(array $queryParams)
    {
        $facilities = Facility::query();
        $response = $this->initializeResponse();

        $searchableFields = ['name', 'tag', 'location'];

        // Iterate through the query parameters to find the first valid query
        foreach ($searchableFields as $field) {
            if (isset($queryParams[$field]) && $this->isValidQuery($queryParams[$field])) {
                $searchResults = $this->performSearch($facilities, $queryParams);
                return $this->handleSearchResponse($searchResults, $response);
            }
        }

        return (new Status\NotFound('Empty Request'))->send();
    }


    /**
     * Initialize the response array.
     *
     * @return array
     */
    private function initializeResponse(): array
    {
        return [
            'facilities' => [],
            'locations' => [],
            'tags' => []
        ];
    }

    /**
     * Validate the search query.
     *
     * @param string $query
     * @return bool
     */
    private function isValidQuery(string $query): bool
    {
        return !empty($query);
    }

    /**
     * Perform the search based on the query.
     *
     * @param $facilities
     * @param string $query
     * @return mixed
     */
    private function performSearch($facilities, array $queryParams)
    {
        if (!empty($queryParams['name'])) {
            $facilities->where('name', 'LIKE', '%' . $queryParams['name'] . '%');
            return $facilities->get();
        }

        // Join with tags if tag name is provided
        if (!empty($queryParams['tag'])) {
            $tags = Tag::query()->where('name', 'LIKE', '%' . $queryParams['tag'] . '%')->get();
            if ($tags) {
                $facilityTag = FacilityTag::query()->where('tag_id', '=', $tags[0]->id)->get();
                $facilities->where('id', '=', $facilityTag[0]->facility_id);
                return $facilities->get(); 
            }
        }

        // Join with locations if location city is provided
        if (!empty($queryParams['location'])) {
            $location = Location::query()->where('city', 'LIKE', '%' . $queryParams['location'] . '%')->get();
            $facilities->where('location_id', 'LIKE', '%' . $location[0]->id . '%');

            return $facilities->get();
        }

    }

    /**
     * Handle the response based on search results.
     *
     * @param mixed $searchResults
     * @param array $response
     * @return void
     */
    private function handleSearchResponse($searchResults, array $response)
    {
        if ($searchResults) {
            $response['facilities'] = $searchResults;
            $response['locations'] = Location::query()->findById($response['facilities'][0]->location_id);
            $response['tags'] = $this->findTags($response['facilities'][0]);

            return (new Status\Ok($response))->send();
        } else {
            return (new Status\NotFound('Facility not found'))->send();
        }
    }

    /**
     * Find tags for a facility.
     *
     * @param Facility $facility
     * @return array
     */
    private function findTags($facility)
    {
        $facilityTags = FacilityTag::query()->where('facility_id', '=', $facility->id)->get();
        $tags = [];

        foreach ($facilityTags as $facilityTag) {
            $tag = Tag::query()->findById($facilityTag->tag_id);
            if ($tag) {
                $tags[] = $tag;
            }
        }

        return $tags;
    }
}