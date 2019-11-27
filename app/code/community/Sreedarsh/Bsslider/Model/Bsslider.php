<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Model_Bsslider extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('bsslider/bsslider');
    }
    
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();
        $this->_attributes = null;

        return $this;
    }
}