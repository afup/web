<?php

class AFUP_Aperos extends Zend_Db_Table
{
    protected function _setup()
    {
        $this->_name    = 'afup_aperos';
        $this->_primary = 'id';
        parent::_setup();
    }    	
    
}