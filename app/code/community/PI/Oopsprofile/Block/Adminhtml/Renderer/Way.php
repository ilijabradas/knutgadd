<?php
class PI_Oopsprofile_Block_Adminhtml_Renderer_Way extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
		public function render(Varien_Object $row)
		{
				//print_r($row->getData($this->getColumn()->getIndex()));
				$data = $row->getData($this->getColumn()->getIndex());
				
				if($data == "")
				{
            return "";
        }
        else
        {
        		$dataTransfer = $row->getDataTransfer();
        		if($row->getDataTransfer() == 'file')
        		{
				    		$guidData = unserialize($row->getGuiData());
				    		return $guidData['file']['type'];
				    }
        		return $row->getDataTransfer();
        }
		}
}
