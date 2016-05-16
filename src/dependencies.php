<?php

// Register Logger and provide variable.
$oContainer['logger'] = function($oC) {
    
    $oLog = new \Monolog\Logger('logger');
    $sLogFilename = __DIR__ . '/../logs'  . '/'. $_ENV['SLIM_MODE'] . '_' .date('Y-m-d').'.log';
    
    $iLogLevel = $oC['settings']['log']['level'];
    $oFileHandler = new \Monolog\Handler\StreamHandler($sLogFilename, $iLogLevel);

    $oLog->pushHandler($oFileHandler);
    $oLog->addInfo("Logger created.");
    return $oLog;
};
$oLog = $oContainer['logger'];

// Register error handler and provide variable.
$oContainer['errorHandler'] = function ($oC) {
    $oError = new API\Error($oC['logger']);
    return $oError;
};
$oError = $oContainer['errorHandler'];


// Register database connection and provide variable.
//$oContainer['db'] = function ($oC) {
 //   try {
        $avDb = $oContainer['settings']['db'];
        $sDSN = sprintf('%s:host=%s;dbname=%s', $avDb['driver'], $avDb['dbhost'], $avDb['dbname']);
        
        \ORM::configure($sDSN);
        \ORM::configure('username', $avDb['username']);
        \ORM::configure('password', $avDb['password']);
        
        foreach($avDb['directives'] as $iType=>$sDirective) {
            \ORM::configure($iType, $sDirective);
        }
        // for now, return arrays instead of result sets
        \ORM::configure('return_result_sets', $avDb['result_sets']);
        \ORM::configure('error_mode', $avDb['error_mode']);
        \ORM::configure('logging', $avDb['logging']);
        \ORM::configure('logger', function($log_string, $query_time) use ($oContainer) {
            $oContainer['logger']->addInfo("IdiORM: $log_string ($query_time)");
        });
        $oContainer['logger']->addInfo("Database connection established via IdiORM.");
 //       return \ORM::getInstance();
/*    
    } catch (\PDOException $e) {
        throw new Exception('Database connection via IdiORM could not be established.');
    }
*/
//};
//$oDb = $oContainer['db'];

?>