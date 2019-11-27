<?php
/**
 * Created by PhpStorm.
 * User: WOLF
 * Date: 11/22/2018
 * Time: 8:34 PM
 */ 
class Nebojsa_BlackFriday_Helper_Data extends Mage_Core_Helper_Abstract {
    protected $_config = null;

    public function __construct()
    {
        $this->_config = Mage::getSingleton('nebojsa_blackfriday/config');
    }

    public function isActive($store = null)
    {
        return $this->_config->active($store);
    }

    public function getBannerBackgroundImage($store = null)
    {
        return $this->_config->banner_background_image($store);
    }

}