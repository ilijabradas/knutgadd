<?php
class Vsourz_Imagegallery_Block_Adminhtml_Imagegallery_Renderer_Status extends
Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row){
		$value = $row->getData($this->getColumn()->getIndex());
		if($value == 1){
			return "Enabled";
		}else{
			return "Disabled";
		}
	}
}