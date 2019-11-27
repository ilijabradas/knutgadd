<?php
class PI_Oopsprofile_Block_Adminhtml_Oopsprofile_Edit_Tab_Profilefilter extends PI_Oopsprofile_Block_Adminhtml_Oopsprofile_Edit_Tab_General
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('oopsprofile/profilefilter.phtml');
        $this->setData(Mage::registry('current_convert_profile')->getData());
    }

    public function getMultipleSelect($data,$value)
    {
    		$selected = $this->getData($data);
    		if(!empty($selected))
    		{
					if (in_array($value, $selected, true)) {
		  				return 'selected="selected"';
		  		}
		  	
		  		else
		  		{
		  				return '';
		  		}
		  	}
		  	return '';
    }
}
