<?php

//bring in the file
if($mode == 'TEST') {
    $hskfile = 'resources/hskchars.test.xml';
    //$dictfile = 'resources/ucd.unihan.flat.test.xml';
    $dictfile = 'resources/ucd.unihan.flat.xml';
} else {
    $hskfile = 'resources/hskchars.xml';
    $dictfile = 'resources/ucd.unihan.flat.xml';
}


//set up XML parsers for both the HSK and dictfiles
$xmlHSK = new XMLReader();
$xmlHSK->open($hskfile);

$xmlDict = new XMLReader();
$xmlDict->open($dictfile);

//parse the HSK chars first
$docHSK = new DOMDocument;
$docDict = new DOMDocument;

//set up defautl variables
$sHSKLvl = "";
$sHSKRef = '';
$sHSKSymbol ='';
$sDefinition = '';
$sPron = '';
$sTone = '';
$sTotalStrokes = '';                   

// move to the first char node in the HSK list
while($xmlHSK->read() && $xmlHSK->name !== 'char');

// go thru HSK chars until the end
$iHSKCount = 0;

while($xmlHSK->name === 'char')
{
    //set up defautl variables
    $sHSKLvl = '';
    $sHSKRef = '';
    $sHSKSymbol ='';
    $sDefinition = '';
    $sPron = '';
    $sTone = '';
    $sTotalStrokes = '';
    $sTraditional = '';
    $successDef = false;
    $successMain = false;
    $successTrad = false;
    $successPron = false;
    
    echo '<br><br>';
    echo ++$iHSKCount . '.';
    // get the char node
    $nodeHSK = simplexml_import_dom($docHSK->importNode($xmlHSK->expand(), true));
    
    
    // read in the attributes
    $sHSKLvl = (string)$nodeHSK[0]['hsk'];
    $sHSKRef = (string)$nodeHSK[0]['ref'];     //$asAttributesHSK['ref'];
    $sHSKSymbol = (string)$nodeHSK[0]['symbol'];
    echo "HSK Level: $sHSKLvl - Ref #: $sHSKRef - Char: $sHSKSymbol - ";

    // (re)open dict then move to first char node in the Dictionary list
    $xmlDict->open($dictfile);
    while($xmlDict->read() && $xmlDict->name !== 'char');
    
    // go thru dictionary until match is found
    $bDictCounter = 0;
    $bDictMatch = false;
    echo "Looking for match in dictionary entry... ";
    while($xmlDict->name === 'char' && $bDictMatch == false)
    {
        ++$bDictCounter;
        $bDictMatch = false;
        
        $nodeDict = simplexml_import_dom($docDict->importNode($xmlDict->expand(), true));
        $sCp = (string)$nodeDict[0]['cp'];
        //if ($bDictCounter > 1) {echo ", $sCp";} else { echo "$sCp";}
        //attn('R', "$sCp == $sHSKRef");
        if($sCp === $sHSKRef)
        {
            //var_dump($nodeDict[0]);
            $bDictMatch = true;
            // store the attributes
            $sDefinition = (string)$nodeDict[0]['kDefinition'];
            $sPron = (string)$nodeDict[0]['kMandarin'];
            $sTotalStrokes = (int)$nodeDict[0]['kTotalStrokes'];
            $sTraditional = (string)$nodeDict[0]['kTraditionalVariant'];
            $sTraditional = str_replace('U+', '', $sTraditional);
            echo "FOUND MATCH in $bDictCounter tries. Def: $sDefinition - Pron: $sPron - Strokes: $sTotalStrokes<br>";
            break;
        }
        // go to next Dict char
        $xmlDict->next('char');
    }
    if(!$bDictMatch) {attn('Y', "No match found.<br>");}
    $xmlDict->close();
    
    // BEGIN DATABASE OPERATIONS:
    
    
    // write data to the database, even if no match in dictionary
    $avRowData = array(
        'unicode' => $sHSKRef,
        'symbol' => $sHSKSymbol,
        'hsklevel' => $sHSKLvl,
        'strokes' => $sTotalStrokes
    );
    $mainRow = $db->t_main()->insert($avRowData);
    
    if(!is_null($mainRow))
    {
        $successMain = true;
        echo "Main row: $mainRow | ";
        // handle tradtional char, if it was found
        if(strlen($sTraditional > 0)) {
            $avRowData = array(
                    'id_main' => $mainRow,
                    'unicode' => $sTraditional  
                );
            $tradRow = $db->t_traditional()->insert($avRowData);
            if(!is_null($tradRow)) {
                 $successTrad = true;
                echo('TRAD variant added.');
            } else {
                attn('Y', 'UNABLE to add trad variant.');
            }
        } else {
            // not necessary, so set to true if there wasn't one
            $successTrad = true;
        }
        
        // there can be multiple pronunciations.
        $asPron = explode(" ", $sPron);
        $iPronCount = 0;
        $iFirstPronRow = 0;
        
        foreach($asPron as $sPron)
        {
            $sPron = trim($sPron);
            if(strlen($sPron) > 0)
            {
                ++$iPronCount;
               
                $sPhonemes = substr($sPron, 0, strlen($sPron)-1);
                $sTone = substr($sPron, -1);
                 echo " $sPhonemes, $sTone | ";
                $avRowData = array(
                    'id_main' => $mainRow,
                    'pron' => $sPhonemes,
                    'tone' => $sTone  
                );
                $pronRow = $db->t_pronunciations()->insert($avRowData);
                if(!is_null($pronRow))
                {
                    echo "Pron row: $pronRow | ";
                    //save row num or first pron to use in recording definitions (see below)
                    if($iPronCount == 1)
                    {
                        $iFirstPronRow = (int)(string)$pronRow;
                        $successPron = true;
                    }  
                } else {
                    if($mode == 'TEST') {
                        var_dump(PDO::errorInfo());
                        var_dump(PDOStatement::errorInfo());
                    }
                }
            }
            if($iFirstPronRow == 0)
            {
                attn('R', "NO pronunciations of $sHSKRef available. ");
            }
        }
         
        // the following is technically incorrect. definitions should be associated
        // with a specific pronunciation, since that is most often the case. for
        // simplicity, however, the definitions will be recorded against the first
        // pronunciation given for the word.
        // there can be multiple definitions.
        $asDef = explode(";", $sDefinition);
        $iDefCount = 0;
        foreach($asDef as $sDef)
        {
            $sDef =trim($sDef);
            if(strlen($sDef) > 0)
            {
                $avRowData = array(
                    'id_pron' => $iFirstPronRow,
                    'definition' => trim($sDef)
                );
                $defRow = $db->t_definitions()->insert($avRowData);
                if(!is_null($defRow))
                {
                     $successDef = true;
                    echo "Definitions row: $defRow. ";
                } else {
                    attn('R', "UNABLE to record char $sHSKRef in t_definitions. ");
                }
            } else {
                attn('Y', "NO definitions of $sHSKRef avaiable. ");
            }
        }
    } else { // writing to t_main failed:
        attn('R', "UNABLE to record char $sHSKRef in t_main. "); 
    }
    
    if($successDef && $successMain && $successTrad && $successPron) {
         attn('G', "SUCCESS! "); 
    }
    // go to next HSK char
    $xmlHSK->next('char');
}


?>