<?php
class PI_Oopsprofile_Model_Dataflow_Profile_History extends Mage_Dataflow_Model_Profile_History
{
    
    protected function _beforeSave()
    {
        if (!$this->getProfileId()) {
            $profile = Mage::registry('current_convert_profile');
            if ($profile) {
                $this->setProfileId($profile->getId());
            }
        }

				//set current user id
        $adminUserId = Mage::getSingleton('admin/session')->getUser()->getId();
        if(!empty($adminUserId))
        {
        		$this->setUserId($adminUserId);
        }

        if(!$this->hasData('user_id')) {
            $this->setUserId(0);
        }

        parent::_beforeSave();
        return $this;
    }
}
