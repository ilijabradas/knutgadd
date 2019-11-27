<?php

class PI_Oopsprofile_Model_Oopsprofile extends Mage_Core_Model_Abstract
{

		protected $_eventPrefix      = 'oopsprofile_profile';
    protected $_eventObject      = 'oopsprofile';

    public function _construct(){
        parent::_construct();
        $this->_init('oopsprofile/oopsprofile');
    }


    public function loadByProfile($profile){

			if(is_numeric($profile)){
				$profile = Mage::getModel('dataflow/profile')->load($profile);
			}
			$collection = Mage::getResourceModel('oopsprofile/oopsprofile_collection');
			$collection->addFieldToFilter('dataflow_profile_id', $profile->getId());

			$oopsProfile = $collection->getFirstItem();
			return $oopsProfile;
    }
}
