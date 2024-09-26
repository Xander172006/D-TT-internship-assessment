<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/test', App\Controllers\IndexController::class . '@test');
$router->get('/', App\Controllers\IndexController::class . '@test');

$router->get('/location', App\Controllers\LocationController::class . '@index');
$router->get('/location/(\w+)', App\Controllers\LocationController::class . '@show');
$router->post('/location/create', App\Controllers\LocationController::class . '@create');

$router->get('/facility', App\Controllers\FacilityController::class . '@index');
$router->get('/facility/(\w+)', App\Controllers\FacilityController::class . '@show');
$router->post('/facility/create', App\Controllers\FacilityController::class . '@create');


