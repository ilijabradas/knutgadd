<?php

class PI_Oopsprofile_Model_Mysql4_Oopsprofile_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('oopsprofile/oopsprofile');
    }
}
