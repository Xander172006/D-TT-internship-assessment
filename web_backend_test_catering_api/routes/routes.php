<?php

/** @var Bramus\Router\Router $router */
$repository = new App\Repository\FacilityRepository();

// Define routes here
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/', App\Controllers\IndexController::class . '@test');

$router->get('/location', App\Controllers\LocationController::class . '@index');
$router->get('/location/(\w+)', App\Controllers\LocationController::class . '@show');
$router->post('/location/create', App\Controllers\LocationController::class . '@create');

$router->get('/facility', App\Controllers\FacilityController::class . '@index');
$router->get('/facility/(\w+)', App\Controllers\FacilityController::class . '@show');
$router->post('/facility/create', App\Controllers\FacilityController::class . '@create');
$router->put('/facility/update/(\d+)', App\Controllers\FacilityController::class . '@update');
$router->delete('/facility/delete/(\d+)', App\Controllers\FacilityController::class . '@delete');

// example:
// http://localhost/api/search?name=Hahn&tag=cutsom-tag

// make search method
$router->get('/search', function() use ($repository) {
    $queryParams = $_GET;
    return $repository->search($queryParams);
});

