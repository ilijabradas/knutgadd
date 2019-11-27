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
 * Checkout Country selector Block
 */
class NWT_KCO_Block_Country extends Mage_Checkout_Block_Onepage_Abstract
{

    public function getAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function getAllowCountries()
    {
        return Mage::helper('nwtkco')->getCountries();
    }

}
