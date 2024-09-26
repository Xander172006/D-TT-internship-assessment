<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Config
$config = require_once './config/config.php';

// Services
require_once './config/services.php';

use App\Factory\LocationFactory;
use App\Factory\FacilityFactory;
use App\Factory\TagFactory;
use App\Factory\FacilityTagFactory;


// Factories
(new LocationFactory())->createMany(10);
(new TagFactory())->createMany(10);
(new FacilityFactory())->createMany(10);
(new FacilityTagFactory())->createMany(10);