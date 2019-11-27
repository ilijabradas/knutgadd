<?php 
class Vsourz_Imagegallery_Block_Imagegallery extends Mage_Catalog_Block_Product_Abstract{
	public function getImages(){
		$catId = $this->getCategoryId();
		return $imageCollection = Mage::getModel('imagegallery/imagegallery')->getImageCollection($catId);
	}
	public function getCategoryData(){
		$catId = $this->getCategoryId();
		return $catCollection = Mage::getModel('imagegallery/imagegallery')->getCategoryCollection($catId);
	}
}