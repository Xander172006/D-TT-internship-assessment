<?php

use App\Plugins;
use App\Plugins\Di\Factory;

$di = Factory::getDi();

$di->setShared('router', function () {
    return new Bramus\Router\Router();
});

$di->setShared('db', function () use ($config) {
    $dbConfig = $config['db'];
    $connectionInterface = new Plugins\Db\Connection\Mysql(
        $dbConfig['host'],
        $dbConfig['database'],
        $dbConfig['username'],
        $dbConfig['password'],
    );
    $db = new Plugins\Db\Db($connectionInterface);
    $dbAdapter = new Plugins\Db\Adapters\MySql();
    $dbAdapter->setDb($db);
    return $db;
});

$di->setShared('locationService', function () use ($di) {
    return new App\Services\LocationService();
});

$di->setShared('facilityService', function () use ($di) {
    return new App\Services\FacilityService();
});