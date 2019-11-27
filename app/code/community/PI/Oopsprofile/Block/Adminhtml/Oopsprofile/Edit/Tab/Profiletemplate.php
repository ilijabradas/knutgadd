<?php
class PI_Oopsprofile_Block_Adminhtml_Oopsprofile_Edit_Tab_Profiletemplate extends PI_Oopsprofile_Block_Adminhtml_Oopsprofile_Edit_Tab_General
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('oopsprofile/profiletemplate.phtml');
        $this->setData(Mage::registry('current_convert_profile')->getData());
    }
}

