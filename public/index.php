<?php

// Prepare app
require_once __DIR__ . '/../src/bootstrap.php';

// Register dependencies
require_once __DIR__ . '/../src/dependencies.php';

// Register middleware
require_once __DIR__ . '/../src/middleware.php';

// Register routes
require_once __DIR__ . '/../src/routes.php';

// Run app
$response = $oApp->run();

?>