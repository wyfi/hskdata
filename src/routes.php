<?php

use \Slim\Http\Request;
use \Slim\Http\Response;

 
/*
// API route group
$app->group('/api', function () use ($app, $log) {

    // Version group
    $app->group('/v1', function () use ($app, $log) {
        // GET routes - use filter for char set and sort
        
        // Chars group
        $app->group('/chars', function () use ($app, $log) {
        // GET routes - use filter for char set and sort
        
        $app->get('/',
            function ($request, $response) use ($app, $log) {
            //returns list of all chars
        });

        $app->get('/{id}',
            function ($request, $response, $args) use ($app, $log) {
            //returns info about a specific char
        });
        
        $app->get('/{id}/sound',
            function ($request, $response, $args) use ($app, $log) {
            //returns a level or levels - uses regex - or :
            
        });
        
        $app->get('/{id}/tone',
            function ($request, $response, $args) use ($app, $log) {
            //returns a level or levels - uses regex - or :
            
        });
        
        $app->get('/{id}/radicals',
            function ($request, $response, $args) use ($app, $log) {
            //returns a level or levels - uses regex - or :
            
        });
        
        $app->get('/{id}/components',
            function ($request, $response, $args) use ($app, $log) {
            //returns a level or levels - uses regex - or :
            
        });
        
        $app->get('/{id}/definition',
            function ($request, $response, $args) use ($app, $log) {
            //returns a level or levels - uses regex - or :
            
        });
        
        $app->get('/hsk',
            function ($request, $response) use ($app, $log) {
            //returns list of all HSK chars. in v1 equiv to #2
        });
        
        $app->get('/hsk/{level}',
            function ($request, $response, $args) use ($app, $log) {
            //returns a level or levels - uses regex - or :
        });
        
    });

});
*/

// Route for testing basic functionality
$oApp->get("/", function(\Slim\Http\Request $req, \Slim\Http\Response $res) {
    $oDb = $this->db;
    $iCount = count($oDb->t_main);
    //echo "count: $iCount";
    $this->logger->addInfo("...and it has $iCount rows.");
    return $res;
});

// Route for testing exceptions
$oApp->get('/exception', function ($req, $res, $args) {
    // errorHandler will trap this Exception
    throw new Exception("An error happened here", 1000);
});


?>
