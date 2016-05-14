<?php

function attn($sLvl,$sText) {
    
    $color = '#FFFFFF';
    switch($sLvl) {
        case 'R':
            $bgcolor = '#FF0000';
            $color = '#FFFFFF';
            break;
        case 'Y':
            $bgcolor = '#FFFF00';
            $color = '#000000';
            break;
        case 'G':
            $bgcolor = '#00FF00';
            $color = '#FFFFFF';
            break;
        
    }
    echo "<span style=\"background-color:$bgcolor;color:$color;\">$sText</span>";
    
}

?>