<?php

$mode = '';


ini_set('max_execution_time', 2000);
ini_set('default_charset', 'UTF-8');
    
require 'vendor/autoload.php';
require 'NotORM.php';
require 'resources/utilties.php';

// establish connection to database
$directive = array(
                   PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                  );
$host = 'mysql:dbname=language_hskdata;host=hskdata.animusloci.com';
$user = 'language_hskdata';
$pw = 'skdataH7397';
$pdo = new PDO($host, $user, $pw, $directive);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db = new NotORM($pdo);

header('Content-Type: text/html; charset=utf-8');

echo "Beginning test<br>";
include_once 'importUnihanXML.php';
echo "Test ended<br>";