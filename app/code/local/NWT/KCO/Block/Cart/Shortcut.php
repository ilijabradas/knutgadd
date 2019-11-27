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
 *
 * Cart Shortcut block
 * Add Klarna Checkout buttons, remove others
 *
 */

class NWT_KCO_Block_Cart_Shortcut extends Mage_Core_Block_Template
{


    //remove children from parent when our block is added as child:D
    public function setParentBlock(Mage_Core_Block_Abstract $parentBlock)
    {

        if($parentBlock && Mage::helper('nwtkco')->removeCartCheckoutButtons()) { //this will test ifEnabled
                $parentBlock->unsetChildren();
        }
        return parent::setParentBlock($parentBlock);

    }


    public function getCheckoutUrl()
    {
        return Mage::helper('nwtkco')->getCheckoutUrl();
    }

    public function isDisabled()
    {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function isPossibleKlarnaCheckout()
    {
        //TODO: check if we have any Klarna allowed country
        return Mage::helper('nwtkco')->addKlarnakassanButton(); //this will test also if enabled
    }

}
