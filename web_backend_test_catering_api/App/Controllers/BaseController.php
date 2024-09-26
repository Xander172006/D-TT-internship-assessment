<?php


namespace App\Controllers;

use App\Plugins\Di\Injectable;
use App\Plugins\Http\Request;

class BaseController extends Injectable {
    public Request $request;
    public function __construct() {
        $this->request = new Request();
    }
}
