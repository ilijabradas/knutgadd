<?php
class Vsourz_Imagegallery_Block_Adminhtml_Category_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form{
	 protected function _prepareForm() {
		if (Mage::registry('category_data')) {
			$data = Mage::registry('category_data')->getData();
		} else {
			$data = array();
		}
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('imagegallery_category', array('legend' => Mage::helper('imagegallery')->__('Caption Information')));
		
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
		$wysiwygConfig->addData(array('add_variables' => false,
			'add_widgets' => false,
			'add_images' => false,
			'directives_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
			'directives_url_quoted' => preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')),
			'widget_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'),
			'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
			'files_browser_window_width' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
			'files_browser_window_height' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
		));
		
		$fieldset->addField('category_title', 'text', array(
			'label' => Mage::helper('imagegallery')->__('Category Title'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'category_title',
		));
		
		$fieldset->addField('category_img', 'image', array(
          'label' => Mage::helper('imagegallery')->__('Category Image'),
		  'required' => false,
		  'name' => 'category_img',
		  'note' => '(*.jpg, *.jpeg, *.png, *.gif)',
        ));
		
		$fieldset->addField('category_description', 'editor', array(
			'name' => 'category_description',
			'label' => Mage::helper('imagegallery')->__('Category Description'),
			'title' => Mage::helper('imagegallery')->__('Category Description'),
			'style' => 'width:400px; height:250px;',
			'config' => $wysiwygConfig,
			'required' => false,
			'wysiwyg' => true
		));
		
		$form->setValues($data);
		return parent::_prepareForm();
	}
}