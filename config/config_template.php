<?php
/**
 * Development configuration
 */
// Set the default time zone for use by the logger
date_default_timezone_set('UTC');

 // Set database related configuration
$avConfig['db'] = array(
    'driver' => '',
    'dbhost' => '',
    'dbname' => '',
    'username' => '',
    'password' => '',
    'directive' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
    'attributes' => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        )
);

$avConfig['db']['dsn'] = sprintf(
    '%s:host=%s;dbname=%s',
    $avConfig['db']['driver'],
    $avConfig['db']['dbhost'],
    $avConfig['db']['dbname']
);

// Set application mode configuration
$avConfig['app']['mode'] = $_ENV['SLIM_MODE'];

// Set logger configuration
if($_ENV['SLIM_MODE'] == 'development') {
    $avConfig['log']['level'] = Monolog\Logger::DEBUG;
} else {
    $avConfig['log']['level'] = Monolog\Logger::WARNING;
}

// Cache TTL in seconds
$avConfig['app']['cache.ttl'] = 60;

// Max requests per hour
$avConfig['app']['rate.limit'] = 1000;

?>