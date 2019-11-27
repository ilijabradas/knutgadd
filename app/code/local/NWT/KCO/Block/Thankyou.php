<?php
/**
 * Klarna Checkout extension
 *
 * @category    NWT
 * @package     NWT_KCO
 * @copyright   Copyright (c) 2015 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     http://nordicwebteam.se/licenses/nwtcl/1.0.txt  NWT Commercial License (NWTCL 1.0)
 * 
 *
 */

/**
 * Klarna iframe thankyou (success) Block
 * shown when the checkout is complete and the order is place
 */
class NWT_KCO_Block_Thankyou extends Mage_Checkout_Block_Onepage_Success
{

    public function getKlarnaOrder() {
        return Mage::registry('KlarnaOrder');
    }
    
    public function isTest() {
        return $this->getRequest()->getParam('nwtkco') == 'test';
    }


}
