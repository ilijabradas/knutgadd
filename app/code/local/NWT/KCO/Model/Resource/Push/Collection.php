<?php
class NWT_Unifaun_Model_Resource_Orderhistory_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('nwtunifaun/orderhistory');
    }
}
