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
 * Klarna Push (history) model
 */

class NWT_KCO_Model_Push extends Mage_Core_Model_Abstract
{

    const ERR_KLARNA_FETCH              = 100; //cannot fetch klarna order

    public function _construct() {
        parent::_construct();
        $this->_init('nwtkco/push');
    }


    public function loadByKid($kID,$test,$store) {
        $pKey = $kID.'|'.($test>0?1:0).'|'.$store;
        return $this->load($pKey,'kid');
    }

    static function getRequest($kID,$test,$store,$prefix) {

        $pKey = $kID.'|'.($test>0?1:0).'|'.$store;
        $push = Mage::getModel('nwtkco/push')->load($pKey,'kid')->setIsAlreadyStarted(true)->setIsAlreadyFetched(true);


        if(!$push->getId()) {
            try {
                $currentTime = Varien_Date::now();
                $push->setKid($pKey)->setOrigin($prefix)->setCreatedAt($currentTime)->save();
                $push->setIsAlreadyStarted(false);
            } catch(Exception $e) {
                //duplicate key? try to reload
                $push->load($pKey,'kid');
                if(!$push->getId()) {
                    //nope, no duplicate key, cannot do nothing
                    throw $e;
                }
            }
        } else {
            $push->setIsAlreadyPlaced(true);
        }

        $pushID = $push->getId();

        if(!$push->getMarshal()) { //we do not fetch Klarna Order
            try {
                $klarnaOrder = Mage::getModel('nwtkco/klarna_order')->setTestMode($test>0)->fetchForUpdate($kID,null);
            } catch(Exception $e) {
                throw new Exception("Cannot fetch Klarna order, {$e->getMessage()}",self::ERR_KLARNA_FETCH);
            }
            $klarnaData = $klarnaOrder->marshal();
            //reload push request; maybe was updated from another request
            $push->load($pushID);
            if(!$push->getMarshal()) {
                $push->setAlreadyFetched(false);
                $push->setMarshal(serialize($klarnaData))->save();
            }
        }
        return $push;
    }

    public function getAge() {
        $now  = time();
        $rup  = strtotime($this->getCreatedAt());
        $age  = round( ($now-$rup)/60, 2); //minutes
        return $age;
    }


}
