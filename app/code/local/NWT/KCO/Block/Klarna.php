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
 * Klarna iframe Block
 */

class NWT_KCO_Block_Klarna extends Mage_Core_Block_Template
{

    public function getKlarnaOrder() {
        return Mage::registry('KlarnaOrder');
    }

}
