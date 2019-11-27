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
 * Klarna Push (history) resource model
 */
class NWT_KCO_Model_Resource_Push extends Mage_Core_Model_Resource_Db_Abstract
{

    public function _construct() {
        $this->_init('nwtkco/push', 'entity_id');
    }

}
