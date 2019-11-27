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
 * Checkout Header Block
 * is shown over the checkout & klarna blocks
 */
class NWT_KCO_Block_Header extends Mage_Core_Block_Template
{

    public function getContent() {
        $content = trim(Mage::helper('nwtkco')->getHeaderText());
        if($content) {
            return Mage::helper('cms')->getPageTemplateProcessor()->filter($content);
        } else {
            return '';
        } 
    }
  
 

}
