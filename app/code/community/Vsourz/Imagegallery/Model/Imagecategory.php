<?php
class Vsourz_Imagegallery_Model_Imagecategory extends Mage_Core_Model_Abstract{
	public function _construct(){
		parent::_construct();
		$this->_init('imagegallery/imagecategory');
	}
}