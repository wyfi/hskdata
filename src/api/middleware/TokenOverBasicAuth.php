<?php
namespace API\Middleware;

class TokenOverBasicAuth
{
    private $_asRoots;
    private $_sRealm;
    private $_oDb; 
    
    //public function __construct($oDb, $asRoots)
    public function __construct($oApp) 
    {
       // asRoots contains acceptable uri roots, tested below.
       $avSettings = $oApp->getContainer()['settings'];
        
       $this->_asRoots      = $avSettings['app']['roots'];
       $this->_sRealm       = $avSettings['app']['realm'];
       $this->_oApp         = $oApp;
       $this->_iInterval    = $avSettings['app']['logout_interval'];
       $this->_sPeriod      = $avSettings['app']['logout_period'];
       
    }

    
    public function __invoke($oRequest, $oResponse, $oNext)
    {
        
        // check that the INCOMING REQUEST matches our URI patter (eg. /api/v1/)
        $bMatch = false;

        foreach($this->_asRoots as $sRoot) { 
            $res = preg_match('|^' . $sRoot . '.*|', $oRequest->getUri()->getPath());
            if($res) $bMatch = true;   // the route root is valid
        }   
            
        if($bMatch) { 
                
            // check that this is a GET request - API doesn't handle other types.
            if($oRequest->isGet()) {
                 
                // get the user
                //var_dump($_SERVER);
                $asAuthUser = $oRequest->getHeader('PHP_AUTH_USER');
                $asAuthKey = $oRequest->getHeader('PHP_AUTH_PW');
               
                if(!(($asAuthUser[0] && $asAuthKey[0]) && $this->verify($asAuthUser[0],$asAuthKey[0] ))) {
                    $sRealm = $this->_sRealm;
                    $oNewResponse = $oResponse->withStatus(401)
                        ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $this->_sRealm))
                        ->write('Please authenticate for me ');
                    return $oNewResponse;
                }
                // criteria met - process thru next middleware
                $oNext($oRequest, $oResponse);
                return $oResponse;
            }
        }
        $oResponse->withStatus(403)
                   ->write('Invalid request');
        return $oResponse;
    }
    
    private function verify($asAuthUser, $asAuthKey)
    {
        $iInterval = $this->_iInterval;
        $iPeriod = strtoupper($this->_iPeriod);
        
        $vUser = \ORM::for_table('t_users')
            ->where('apiuser',$asAuthUser)
            ->where('apikey',$asAuthKey)
            ->where_raw('(`expiry` > NOW())')
            ->where_raw("(`last_login` <= DATE_SUB(NOW(), INTERVAL $iInterval $iPeriod))");
         
        if ($vUser->count()) {
            $vUser->find_one();
            echo   $vUser->apikey;
            echo   $vUser->apiuser;
            return true;
        } else {
            echo 'b';
            return false;
        }
    }
}
?>