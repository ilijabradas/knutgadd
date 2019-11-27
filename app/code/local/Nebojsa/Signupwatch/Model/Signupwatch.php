<?php

class Nebojsa_Signupwatch_Model_Signupwatch extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('signupwatch/signupwatch');
    }

}
