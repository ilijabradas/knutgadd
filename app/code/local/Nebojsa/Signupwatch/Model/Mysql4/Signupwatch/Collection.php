<?php

class Nebojsa_Signupwatch_Model_Mysql4_Signupwatch_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('signupwatch/signupwatch');
    }

    public function prepareSummary()
    {
        $this->setConnection($this->getResource()->getReadConnection());

        $this->getSelect()
            ->from(array('main_table' => 'signupwatch'), '*');

        return $this;
    }

    public function getView($signupwatch_id)
    {

        $this->setConnection($this->getResource()->getReadConnection());
        $this->getSelect()
            ->from(array('main_table' => 'signupwatch'), '*')
            ->where('signupwatch_id = ?', $signupwatch_id);

        return $this;
    }

}
