<?php
class Vsourz_Imagegallery_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct() {
		parent::__construct();
		$this->setId('category_tabs');
		$this->setDestElementId('edit_form'); // this should be same as the form id in Form.php
		$this->setTitle(Mage::helper('imagegallery')->__('Image Category'));
	}
	protected function _beforeToHtml() {
		$this->addTab('form_section', array(
			'label' => Mage::helper('imagegallery')->__('Category Detail'),
			'title' => Mage::helper('imagegallery')->__('Category Detail'),
			'content' => $this->getLayout()->createBlock('imagegallery/adminhtml_category_edit_tabs_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	} 
}