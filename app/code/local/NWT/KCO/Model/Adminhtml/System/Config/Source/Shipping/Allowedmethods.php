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
 * Model Source for Shipping
 * Return allowed carriers, without "empty" one
 */
class NWT_KCO_Model_Adminhtml_System_Config_Source_Shipping_Allowedmethods extends Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods
{
    /**
     * Return array of active carriers.
     *
     * @param bool $isMultiselect
     * @return array
     */

    public function toOptionArray($isMultiselect=false)
    {

        
        $options = parent::toOptionArray(true);
        if($isMultiselect) {
            //remove first option (empty one)
            array_pop($options);
        }
        return $options;
    }
}
