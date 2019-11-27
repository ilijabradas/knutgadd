<?php
class Vsourz_Imagegallery_Model_Imagegallery extends Mage_Core_Model_Abstract{
	public function getImageCollection($catId){
		$categoryId = $catId;
		$order = Mage::getStoreConfig('imagegallery/settings/order');
		if($order=='Ascending')
		{
			$column='imagedetail_id';
			$position='ASC';
		}
		else if($order=='Descending')
		{
			$column='imagedetail_id';
			$position='DESC';
		}
		else
		{
			$column='position';
			$position='ASC';
		}
		$imageCollection = Mage::getModel('imagegallery/imagedetail')->getCollection()
		->addFieldToFilter('status','1')
		->setOrder($column,$position)
		->addFieldToFilter('category_id',$categoryId);
		return $imageCollection;
	}
	public function getCategoryCollection($catId){
		$categoryId = $catId;
		$categoryCollection = Mage::getModel('imagegallery/imagecategory')->getCollection()
		->addFieldToFilter('imagecategory_id',$catId);
		return $categoryCollection;
	}
}