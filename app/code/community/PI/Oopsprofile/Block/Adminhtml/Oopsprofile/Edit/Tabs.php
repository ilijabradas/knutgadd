<?php

class PI_Oopsprofile_Block_Adminhtml_Oopsprofile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('convert_profile_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Import/Export Profile'));
    }

    protected function _beforeToHtml()
    {
        $profile = Mage::registry('current_convert_profile');

        $wizardBlock = $this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile_edit_tab_general');
        $wizardBlock->addData($profile->getData());

        $new = !$profile->getId();

        $this->addTab('wizard', array(
            'label'     => Mage::helper('adminhtml')->__('General Information'),
            'content'   => $wizardBlock->toHtml(),
            'active'    => true,
        ));

        $this->addTab('profileway', array(
            'label'     => Mage::helper('adminhtml')->__('Import/Export Way'),
            'content'   => $this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile_edit_tab_profileway')->toHtml(),
        ));

        $this->addTab('profiletemplate', array(
            'label'     => Mage::helper('adminhtml')->__('Format and Fields'),
            'content'   => $this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile_edit_tab_profiletemplate')->toHtml(),
        ));

        $this->addTab('profilefilter', array(
            'label'     => Mage::helper('adminhtml')->__('Apply Filters'),
            'content'   => $this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile_edit_tab_profilefilter')->toHtml(),
        ));

        if (!$new) {

						$this->addTab('view', array(
				        'label'     => Mage::helper('adminhtml')->__('Profile Actions XML'),
				        'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tab_view')->initForm()->toHtml(),
				    ));

				    $this->addTab('run', array(
				            'label'     => Mage::helper('adminhtml')->__('Run Profile'),
				            'content'   => $this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile_edit_tab_run')->toHtml(),
				    ));

				    $this->addTab('history', array(
                'label'     => Mage::helper('adminhtml')->__('Profile History'),
                'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml(),
            ));
				}


        return parent::_beforeToHtml();
    }
}
