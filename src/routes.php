<?php

use \Slim\Http\Request;
use \Slim\Http\Response;


// API route group (/api)
// the group identifiers must match the roots settings!!!
$oApp->group('/api', function () use ($oApp, $oLog) {

    // Version group (/v1)
    $oApp->group('/v1', function () use ($oApp, $oLog) {
        // GET routes - use filter for char set and sort
        
        // Chars group
        $oApp->group('/chars', function () use ($oApp, $oLog) {
        // GET routes - use filter for char set and sort
        
            //returns list of all chars
            $oApp->get('/',
                function ($request, $response) use ($oApp, $oLog) {
                $oCharacters = new Characters($this->db);
            });
            /*
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
            */
        });
    });
});


// Route for testing basic functionality
$oApp->get("/", function(Request $req, Response $res) {
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
