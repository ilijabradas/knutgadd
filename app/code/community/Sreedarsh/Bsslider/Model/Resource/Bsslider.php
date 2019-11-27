<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Model_Resource_Bsslider extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('bsslider/bsslider', 'id');
    }

}
