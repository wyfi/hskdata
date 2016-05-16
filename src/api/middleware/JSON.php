<?php
namespace API\Middleware;

class JSON
{
    private $_root; 
    
    public function __construct($root = '')
    {
        $this->_root = $root;
    }
    
    public function __invoke($oRequest, $oResponse, $oNext)
    {
         echo 'in JSON';
        // check that the INCOMING REQUEST matches our URI patter (eg. /api/v1/)
        $res = preg_match('|^' . $this->_root . '.*|', $oRequest->getUri()->getPath());
        
        if ($res) {
            
            // check that this is a GET request - API doesn't handle other types.
            if($oRequest->isGet()) {

                // force the response headers to Json.
                try {
                    $oNewResponse = $oResponse->withJson('howdy!', 200);
                    $response = $oNext($oRequest, $oResponse);
                    return $oNewResponse;
                }
                catch (\RuntimeException $e) {
                   throw new Exception('Unable to encode JSON response.'); 
                }
            }
        }
        $oResponse->withStatus(415);
    }       
    
}
?>