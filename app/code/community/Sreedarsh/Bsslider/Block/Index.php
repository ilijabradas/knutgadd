<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Block_Index extends Mage_Core_Block_Template {

    public function getSlider()
    {
        $collection = Mage::getModel('bsslider/bsslider')->getCollection()
        ->setOrder('id', 'DESC')
        ->addFieldToFilter('status', 1);
        return $collection;
    }

}
