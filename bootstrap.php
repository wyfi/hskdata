<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

use Slim\App;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Flynsarmy\SlimMonolog\Log\MonologWriter;
//use API\Application;  extends Slim\Slim
use API\Middleware\TokenOverBasicAuth;

// Init application mode.  
if (empty($_ENV['SLIM_MODE'])) {
    $_ENV['SLIM_MODE'] = (getenv('SLIM_MODE')) ? getenv('SLIM_MODE') : 'development';
}

// Init and load configuration. Find a config file specific to the mode; otherwise use default
$avConfig = array();
$sConfigFile = dirname(__FILE__) . '/config/' . $_ENV['SLIM_MODE'] . '.php';

if (is_readable($sConfigFile)) {
    require_once $sConfigFile;
} else {
    require_once dirname(__FILE__) . '/config/default.php';
}

//var_dump($avConfig);
// Create Slim spplication
$oApp = new App(['config' => $avConfig]);

// Get container and add logger 
$oContainer = $oApp->getContainer();

// Create Logger 
$oContainer['logger'] = function($oC) {
    $oLogger = new \Monolog\Logger('logger');
    $sLogFilename = realpath(__DIR__ . '/./logs') . '/'. $_ENV['SLIM_MODE'] . '_' .date('Y-m-d').'.log';
    $iLogLevel = $oC['config']['log']['level'];
    $oFileHandler = new \Monolog\Handler\StreamHandler($sLogFilename, $iLogLevel);
    $oLogger->pushHandler($oFileHandler);
    return $oLogger;
};

// Establish database connection
try {
    $oContainer['db'] = function ($oC) {
        $avDb = $oC['config']['db'];
        $sDSN = $avDb['dsn'];
        $oPDO = new PDO($sDSN, $avDb['username'], $avDb['password'], $avDb['directive']);             
        foreach($avDb['attributes'] as $iType=>$iAttribute) {
            $oPDO->setAttribute($iType, $iAttribute);
        }
        $oDb = new \NotORM($oPDO);
        $oC['logger']->addInfo("Database connection established.");
        return $oDb;
    };

} catch (\PDOException $e) {
    //$container['logger']->addInfo($e->getMessage());
}


// Cache Middleware (inner)
//$app->add(new API\Middleware\Cache('/api/v1'));

// Parse JSON body
//$app->add(new \Slim\Middleware\ContentTypes());

// Manage Rate Limit
//$app->add(new API\Middleware\RateLimit('/api/v1'));

// JSON Middleware
//$app->add(new API\Middleware\JSON('/api/v1'));

// Auth Middleware (outer)
//$app->add(new API\Middleware\TokenOverBasicAuth(array('root' => '/api/v1')));
?>