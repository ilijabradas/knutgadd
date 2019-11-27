<?php
class Vsourz_Imagegallery_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'adminhtml_category';
		$this->_blockGroup = 'imagegallery'; 
		/* please not this is the block group the grid is called in this fashion: ($this->_blockGroup._.$this->_controller._.grid) */
		$this->_headerText = Mage::helper('imagegallery')->__('Category Manager');
		$this->_addButtonLabel = Mage::helper('imagegallery')->__('Add Category');
		parent::__construct(); 
	}
}
