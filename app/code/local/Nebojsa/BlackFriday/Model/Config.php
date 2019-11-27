<?php

/**
 * Created by PhpStorm.
 * User: WOLF
 * Date: 11/20/2018
 * Time: 2:55 PM
 */
class Nebojsa_BlackFriday_Model_Config
{

    const CONFIG_XML_PATH_ACTIVE = 'nebojsa_blackfriday/settings/active';
    const CONFIG_XML_PATH_BANNER_BACKGROUND_IMAGE = 'nebojsa_blackfriday/settings/banner_background_image';

    public function active($store = null)
    {
        return (bool)Mage::getStoreConfig(self::CONFIG_XML_PATH_ACTIVE, $store);
    }

    public function banner_background_image($store = null)
    {
        return (string)Mage::getStoreConfig(self::CONFIG_XML_PATH_BANNER_BACKGROUND_IMAGE, $store);
    }


}