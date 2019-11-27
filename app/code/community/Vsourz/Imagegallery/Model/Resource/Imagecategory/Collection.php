<?php
class Vsourz_Imagegallery_Model_Resource_Imagecategory_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{
	public function _construct(){
		$this->_init('imagegallery/imagecategory');
	}
}