<?php
class Vsourz_Imagegallery_Model_Categoryval extends Mage_Core_Model_Abstract{
	public function getCategoryVal(){
		$catVal = Mage::getModel('imagegallery/imagecategory')->getCollection();
		$data = $catVal->getData();
		$dropVal = array();
			$dropVal[''] = "Please Select";
		foreach($data as $value){
			$dropVal[$value['imagecategory_id']] = $value['category_title'];
		};
		return $dropVal;
	}
}