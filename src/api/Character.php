<?php

namespace API;

class Character {
    
    private $_iId;
    private $_sUnicode;
    private $_sSymbol;
    private $_iHSKLevel;
    private $_iStrokes;
    private $_iFrequency ;

    public function __construct(\NotORM $oDb) {
        
        
    }
    
    // Getters
    public function getUnicode() {
        return $this->_sUnicode;
    }
    public function getSymbol() {
        return $this->_sSymbol;
    }
    public function getHSKlevel() {
        return $this->_iHSKLevel;
    }
    public function getStrokes() {
        return $this->_iStrokes;
    }
    public function getFrequency() {
        return $this->_iFrequency;
    }
    
    

    
}
?>
