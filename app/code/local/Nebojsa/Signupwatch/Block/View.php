<?php

class Nebojsa_Signupwatch_Block_View extends Mage_Core_Block_Template {

	protected $_signupwatchCollection = null;

	public function getLoadedSignupwatchCollection() {
		return $this->_getSignupwatchCollection();
	}

	public function _getSignupwatchCollection() {
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$query = "SELECT * FROM `signupwatch` WHERE country_code IS NOT NULL;";
		$signupwatchs = $read->query($query)->fetchAll();

		return $signupwatchs;
	}

}
