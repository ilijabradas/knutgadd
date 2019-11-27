<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Model_Resource_Bsslider_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct()
    {
        parent::_construct();
        $this->_init('bsslider/bsslider');
    }

    public function getSliderdetails($id = 0)
    {
        $query = $this->addFieldToFilter(
                'id', array(
            'eq' => $id
                )
        );

        return $query;
    }

}
