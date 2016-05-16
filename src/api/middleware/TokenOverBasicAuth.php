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
       $this->_oLog         = $oApp->getContainer()['logger'];
    }

    public function __invoke($oRequest, $oResponse, $oNext)
    {
        $L = $this->_oLog;
        // check that the INCOMING REQUEST matches our URI patter (eg. /api/v1/)
        $bMatch = false;

        foreach($this->_asRoots as $sRoot) {
            $res = preg_match('|^' . $sRoot . '.*|', $oRequest->getUri()->getPath());
            if($res) $bMatch = true;   // the route root is valid
        }
        $L->addInfo($oRequest->getUri()->getPath());
              
        if($bMatch) { 
                
            // check that this is a GET request - API doesn't handle other types.
            if($oRequest->isGet()) {
                 
                // Get the user, if there is one
                $asAuthUser = $oRequest->getHeader('PHP_AUTH_USER');
                $asAuthKey = $oRequest->getHeader('PHP_AUTH_PW');
                
                $sStatus = $this->verify($asAuthUser[0],$asAuthKey[0] ); //EXP, STL, NA or OK
               
                switch($sStatus) {
                    case 'STL':
                    case 'NA':
                        $sRealm = $this->_sRealm; 
                       
                        $oNewResponse = $oResponse->withStatus(401)
                            ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $sRealm))
                            ->write('s');
                        return $oNewResponse;
                        break;
                    case 'EXP':
                        $oNewResponse = $oResponse->withStatus(403)
                            ->write('Your credential has expired. Please contact us for renewal.');
                            return $oNewResponse;
                        break;
                    case 'OK':
                        // Request is good and user is valid - proceed.
                        $oNext($oRequest, $oResponse);
                        return $oResponse;
                        break;
                    //}
                }
            } 
        }
        $oNewResponse = $oResponse->withStatus(403)
            ->write('The request is invalid, phrased incorrectly or not supported.');
        return $oNewResponse;
    }
    
    private function verify($asAuthUser, $asAuthKey)
    {   // not in db at all (always false), in db but expired, all ok
        $L = $this->_oLog;
        $iInterval = $this->_iInterval;
        $sPeriod = strtolower($this->_sPeriod);
        $L->addInfo("PASSED IN: $asAuthUser, $asAuthKey");
        $vUser = \ORM::for_table('t_users')
            ->where('apiuser',$asAuthUser)
            ->where('apikey',$asAuthKey);
         
        if ($vUser->count()) {
            $oUser = $vUser->find_one();
            
            $sApiKey        = $oUser->apikey;
            $sApiUser       = $oUser->apiuser;
            $sExpiry        = $oUser->expiry;
            $sLastLogin     = $oUser->last_login;
            
            $L->addInfo("VALIDATING USER:$sApiUser KEY: $sApiKey");
            $iNow = strtotime('now');
            $iExpiry = strtotime($sExpiry) ? strtotime($sExpiry) : $iNow;
            $iLastLogin = strtotime($sLastLogin) ? strtotime($sLastLogin) : 0;
            $iStaleAt = strtotime($sLastLogin .'+'. "$iInterval $sPeriod") ? strtotime($sLastLogin .'+'. "$iInterval $sPeriod") : 0;
            
            //$L->addInfo("EXPIRY <= NOW: $iExpiry <= $iNow ? If yes, then expired.");
            //$L->addInfo("STALE AT <= NOW: $iStaleAt <= $iNow ? If yes, then stale.");
            //$L->addInfo("LAST LOGIN + $iInterval $sPeriod (StaleAt time): $sLastLogin .'+'. $iInterval $sPeriod = $iStaleAt");
            
            // If token is less than now, it has expired. No challenge
            if($iExpiry <= strtotime('now')) {
                $L->addInfo("EXPIRED! ");
                return 'EXP';
            }
            
            // If stale at time is less than now, token is stale. Just update it.
            // But at least at the moment, they still have the correct UNPW, or they wouldn't be here.
            // UNPW correct because it was Since that is the case, update the login time.
            if($iStaleAt <= strtotime('now')) {
                $L->addInfo("STALE TOKEN - SEND CHALLENGE");
                $sCode = 'STL';
            } else {
                $L->addInfo("USER OK");
                $sCode = 'OK';
            }
            
            //update user login time
            $oUser->set_expr('last_login', 'UTC_TIMESTAMP()');
            $oUser->save();

            return $sCode;
        } else {
            $L->addInfo("NO ACCESS");
            return 'NA';
        }
    }
}
?>