<?php
	Class Sreedarsh_Bsslider_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
		public function render(Varien_Object $row)
		{
			$image = $row->getData($this->getColumn()->getIndex());
			return "<img height='110px' src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$image."' />";
		}
	}
?>