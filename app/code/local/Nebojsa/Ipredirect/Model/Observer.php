<?php
/**
 * Created by PhpStorm.
 * User: WOLF
 * Date: 9/11/2018
 * Time: 10:27 AM
 */


class Nebojsa_Ipredirect_Model_Observer
{

    public function ip2location($observer)
    {

//        if (Mage::app()->getStore()->isAdmin()) {
//            return;
//        }
        if (Mage::helper('nebojsa_ipredirect')->getLocationInfoByIp() == "TW") {
            $store = Mage::getModel('core/store')->load('tw', 'code');

            if ($store->getCode() != Mage::app()->getStore()->getCode()) {
                $observer->getControllerAction()->getResponse()->setRedirect($store->getCurrentUrl(false));
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
                strtok($url, '?');

            }
        }
        if (Mage::helper('nebojsa_ipredirect')->getLocationInfoByIp() == "JP") {
            $store = Mage::getModel('core/store')->load('jp', 'code');

            if ($store->getCode() != Mage::app()->getStore()->getCode()) {
                $observer->getControllerAction()->getResponse()->setRedirect($store->getCurrentUrl(false));
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
                strtok($url, '?');

            }
        }

    }
}
