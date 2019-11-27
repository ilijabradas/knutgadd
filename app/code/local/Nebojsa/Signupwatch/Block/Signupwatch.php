<?php
class Nebojsa_Signupwatch_Block_Signupwatch extends Mage_Core_Block_Template {

	public function _prepareLayout() {
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
			$block->setCanLoadTinyMce(true);
		}
		return parent::_prepareLayout();
	}

	public function getSignupwatch() {
		if (!$this->hasData('signupwatch')) {
			$this->setData('signupwatch', Mage::registry('signupwatch'));
		}
		return $this->getData('signupwatch');
	}

}
