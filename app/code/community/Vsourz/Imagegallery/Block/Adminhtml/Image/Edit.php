<?php
class Vsourz_Imagegallery_Block_Adminhtml_Image_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct(){
		parent::__construct();
		$this->_objectId = 'imageid';
		$this->_blockGroup = 'imagegallery';
		$this->_controller = 'adminhtml_image';
		$this->_mode = 'edit';
		$this->_updateButton('save', 'label', Mage::helper('imagegallery')->__('Save Image'));
		$this->_updateButton('delete', 'label', Mage::helper('imagegallery')->__('Delete'));
		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('imagegallery')->__('Save And Continue Edit'),
			'onclick' => 'saveAndContinueEdit()',
			'class' => 'save',
		), -100); 
		
		$this->_formScripts[] ="
			function toggleEditor(){
				if (tinyMCE.getInstanceById('form_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'edit_form');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
				}
			}
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}"; 
	}
	protected function _prepareLayout(){
		parent::_prepareLayout();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		}
	}
	public function getHeaderText() {
		if (Mage::registry('image_data') && Mage::registry('image_data')->getId()) {
			return Mage::helper('imagegallery')->__('Edit Image "%s"', $this->htmlEscape(Mage::registry('image_data')->getImageTitle()));
		} else {
			return Mage::helper('imagegallery')->__('New Image');
		}
	}
	
}