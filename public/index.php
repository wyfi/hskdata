<?php


require_once dirname(__FILE__) . '/../bootstrap.php';

// Begin routes
$oApp->get("/", function(\Slim\Http\Request $req, \Slim\Http\Response $res) {
    $oDb = $this->db;
    $iCount = count($oDb->t_main);
    $this->logger->addInfo("...and it has $iCount rows.");
});

// Run app
$oApp->run();

?>