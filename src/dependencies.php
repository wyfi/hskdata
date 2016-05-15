<?php

// Create Logger
$oContainer['logger'] = function($oC) {
    
    $oLogger = new \Monolog\Logger('logger');
    $sLogFilename = __DIR__ . '/../logs'  . '/'. $_ENV['SLIM_MODE'] . '_' .date('Y-m-d').'.log';
    
    $iLogLevel = $oC['settings']['log']['level'];
    $oFileHandler = new \Monolog\Handler\StreamHandler($sLogFilename, $iLogLevel);

    $oLogger->pushHandler($oFileHandler);
    $oLogger->addInfo("Logger created.");
    return $oLogger;
 
};


$oContainer['errorHandler'] = function ($oC) {
    $oError = new API\Error($oC['logger']);
    return $oError;
};


// Establish database connection
$oContainer['db'] = function ($oC) {
    try {
        $avDb = $oC['settings']['db'];
        $sDSN = sprintf('%s:host=%s;dbname=%s', $avDb['driver'], $avDb['dbhost'], $avDb['dbname']);
        $oPDO = new PDO($sDSN, $avDb['username'], $avDb['password'], $avDb['directive']);

        foreach($avDb['attributes'] as $iType=>$iAttribute) {
            $oPDO->setAttribute($iType, $iAttribute);
        }
        
        $oDb = new \NotORM($oPDO);
        $oC['logger']->addInfo("Database connection established.");
        return $oDb;
    } catch (\PDOException $e) {
        throw new Exception('PDO connection could not be established. ');
    }
};





?>