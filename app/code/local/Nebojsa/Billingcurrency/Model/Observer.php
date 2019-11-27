<?php
/**
 * Ilija Bradas
 *
 * @package     Nebojsa_Billingcurrency
 * @author      Ilija Bradas
 * @copyright   Copyright (c) 2017 Ilija Bradas
 */

class Nebojsa_Billingcurrency_Model_Observer {
	/**
	 * redirects customer to store view based on GeoIP
	 * @param $event
	 */
	public function controllerActionPredispatch($observer) {

		Mage::app()->getStore()->setCurrentCurrencyCode('USD');
		Mage::log('I just made an Billing currency observer!', null, 'system.log', true);
	}
}