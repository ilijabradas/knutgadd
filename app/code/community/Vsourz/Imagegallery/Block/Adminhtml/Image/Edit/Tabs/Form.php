<?php
class Vsourz_Imagegallery_Block_Adminhtml_Image_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form{
	 protected function _prepareForm() {
		if (Mage::registry('image_data')) {
			$data = Mage::registry('image_data')->getData();
		} else {
			$data = array();
		}
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('imagegallery_image', array('legend' => Mage::helper('imagegallery')->__('Caption Information')));
		
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
		
		$fieldset->addField('image_title', 'text', array(
			'label' => Mage::helper('imagegallery')->__('Image Title'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'image_title',
		));
		
		$fieldset->addField('gallery_img', 'image', array(
          'label' => Mage::helper('imagegallery')->__('Gallery Image'),
		  'class' => 'required-entry required-file',
		  'required' => true,
		  'name' => 'gallery_img',
		  'note' => '(*.jpg, *.jpeg, *.png, *.gif)',
        ));
		
		$fieldset->addField('category_id', 'select', array(
          'label' => Mage::helper('imagegallery')->__('Image Category'),
		  'class' => 'required-entry',
		  'required' => true,
		  'name' => 'category_id',
		  'disabled' => false,
          'readonly' => false,
		  'values' => Mage::getModel('imagegallery/categoryval')->getCategoryVal(),
        ));
		
		$fieldset->addField('image_description', 'editor', array(
			'name' => 'image_description',
			'label' => Mage::helper('imagegallery')->__('Image Description'),
			'title' => Mage::helper('imagegallery')->__('Image Description'),
			'style' => 'width:400px; height:250px;',
			'config' => $wysiwygConfig,
			'required' => false,
			'wysiwyg' => true,
		));		
		
		$fieldset->addField('position', 'text', array(
			'label' => Mage::helper('imagegallery')->__('Position'),
			'class' => 'required-entry',
			'required'  => true,
			'name' => 'position',
			'note' => 'Enter the position [in numeric] of image
						<script>
							document.getElementById("position").setAttribute("onkeypress", "return isNumberKey(event)");
							function isNumberKey(evt)
							{
								var charCode = (evt.which) ? evt.which : evt.keyCode;
								if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
								return false;
								return true;
							}
							if(document.getElementById("position").value=="")								
							document.getElementById("position").setAttribute("value", "0");
						</script>',			
		));
		
		$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('imagegallery')->__('Status'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'status',
          'value'  => '0',
          'values' => array('0' => 'Disable','1' => 'Enable'),
          'disabled' => false,
          'readonly' => false,          
        ));
		
		$form->setValues($data);
		return parent::_prepareForm();
	}
}
?>

