<?php

namespace App\Controllers;

use App\Models\Tag;
use App\Repository\FacilityRepository;
use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;

class SearchController extends BaseController {

    /**
     * Controller function used to test whether the project was set up properly.
     * @return void
     */
    public function search() {
       /** @var Bramus\Router\Router $router */
        $repository = new FacilityRepository();
        return $repository->search($this->request->allQuery());
    }
}
