<?php

//$asRoot = $avSettings['app']['roots'];

// Cache Middleware (inner)
//$app->add(new API\Middleware\Cache('/api/v1'));

// Parse JSON body
//$app->add(new \Slim\Middleware\ContentTypes());

// Manage Rate Limit
//$app->add(new API\Middleware\RateLimit('/api/v1'));

// JSON Middleware - not needed unless incoming data
//$oApp->add(new API\Middleware\JSON('/api/v1'));

// Auth Middleware (outer)
$oApp->add(new API\Middleware\TokenOverBasicAuth($oApp));


?>