<?php

class PI_Oopsprofile_Model_Mysql4_Oopsprofile extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the oops_profile_id refers to the key field in your database table.
        $this->_init('oopsprofile/oopsprofile', 'oops_profile_id');
    }
}
