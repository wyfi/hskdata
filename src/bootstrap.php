<?php

// Provide autoloading for classes
require_once __DIR__ . '/../vendor/autoload.php';

// Shorthand the namespaces
use Slim\App;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Flynsarmy\SlimMonolog\Log\MonologWriter;
//use API\Application;  extends Slim\Slim
use API\Middleware\TokenOverBasicAuth;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Init application mode.  
if (empty($_ENV['SLIM_MODE'])) {
    $_ENV['SLIM_MODE'] = (getenv('SLIM_MODE')) ? getenv('SLIM_MODE') : 'development';
}

// Init and load configuration. Find a config file specific to the mode; otherwise use default
$avConfig = array();
$sConfigFile = realpath(__DIR__ . '/../config') . '/' . $_ENV['SLIM_MODE'] . '.php';

if (is_readable($sConfigFile)) {
    require_once $sConfigFile;
} else {
    require_once __DIR__ . '/../config/default.php';
}

// Create Slim spplication
$oApp = new App(['settings' => $avConfig]);

// Get container and add logger 
$oContainer = $oApp->getContainer();


?>