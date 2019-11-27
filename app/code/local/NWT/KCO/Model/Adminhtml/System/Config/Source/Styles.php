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
 * Model Source for Klarna Product widget
 * @see https://developers.klarna.com/en/marketing-tools/design-and-branding
 */
class NWT_KCO_Model_Adminhtml_System_Config_Source_Styles
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> 'Disable (Don\'t show)'),
            array('value'=>'pale', 'label'=> 'Pale (Black text with blue Klarna logo)'),
            array('value'=>'dark', 'label'=> 'Dark (White text with blue Klarna logo)'),
            array('value'=>'deep', 'label'=> 'Deep (White text with white Klarna logo)'),
            array('value'=>'deep-extra', 'label'=> 'Deep Extra (Black text with white Klarna logo)'),
            
            /* ?
            // version 2
            array('value' => 'pale-v2',       'label'=> 'Version 2, Pale (Black text with blue Klarna logo)'),
            array('value' => 'dark-v2',       'label'=> 'Version 2, Dark (White text with blue Klarna logo)'),
            array('value' => 'deep-v2',       'label'=> 'Version 2, Deep (White text with white Klarna logo)'),
            array('value' => 'deep-extra-v2', 'label'=> 'Version 2, Deep Extra (Black text with white Klarna logo)')
            */
        );
    }
}


