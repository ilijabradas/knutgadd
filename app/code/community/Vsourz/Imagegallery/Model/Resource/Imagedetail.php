<?php
class Vsourz_Imagegallery_Model_Resource_Imagedetail extends Mage_Core_Model_Mysql4_Abstract{
    public function _construct(){
        $this->_init('imagegallery/imagedetail','imagedetail_id');
    }
}