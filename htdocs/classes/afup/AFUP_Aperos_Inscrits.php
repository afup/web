<?php

class AFUP_Aperos_Inscrits extends Zend_Db_Table
{
    protected function _setup()
    {
        $this->_name    = 'afup_aperos_inscrits';
        $this->_primary = 'id';
        parent::_setup();
    }    	
    
}