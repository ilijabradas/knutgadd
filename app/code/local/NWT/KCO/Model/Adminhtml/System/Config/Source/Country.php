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
 * Model Source for all allowed Klarna Countries
 */
class NWT_KCO_Model_Adminhtml_System_Config_Source_Country
{

    public function toOptionArray($isMultiselect=false)
    {

        $locales = Mage::getSingleton('nwtkco/klarna_locale')->getLocales();
        
        $return = array();

        if(!$isMultiselect) {
            $return[] = array('value'=>'', 'label'=> '');
        }

        foreach($locales as $key=>$locale) {
            $return[] = array(
                'value'=>$key,
                'label'=>$locale['name']
            );
        }

        return $return;
    }
}
