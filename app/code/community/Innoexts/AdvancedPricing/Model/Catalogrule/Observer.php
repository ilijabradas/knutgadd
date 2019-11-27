<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_AdvancedPricing
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Catalog rule observer
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Catalogrule_Observer 
    extends Mage_CatalogRule_Model_Observer 
{
    /**
     * Get advanced pricing helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Data
     */
    protected function getAdvancedPricingHelper()
    {
        return Mage::helper('advancedpricing');
    }
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    public function getVersionHelper()
    {
        return $this->getAdvancedPricingHelper()->getVersionHelper();
    }
    /**
     * Apply all catalog price rules for specific product
     *
     * @param   Varien_Event_Observer $observer
     * 
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function applyAllRulesOnProduct($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if (!$product || $product->getIsMassupdate()) {
            return $this;
        }
        if (!$this->getVersionHelper()->isGe1800()) {
            $productWebsiteIds = $product->getWebsiteIds();
            $rules = Mage::getModel('catalogrule/rule')->getCollection()->addFieldToFilter('is_active', 1);
            foreach ($rules as $rule) {
                if ($this->getVersionHelper()->isGe1700()) {
                    $websiteIds = array_intersect($productWebsiteIds, $rule->getWebsiteIds());
                    $storeIds = $rule->getStoreIds();
                } else {
                    if (!is_array($rule->getWebsiteIds())) {
                        $ruleWebsiteIds = (array)explode(',', $rule->getWebsiteIds());
                    } else {
                        $ruleWebsiteIds = $rule->getWebsiteIds();
                    }
                    $websiteIds = array_intersect($productWebsiteIds, $ruleWebsiteIds);
                    if (!is_array($rule->getStoreIds())) {
                        $storeIds = (array) explode(',', $rule->getStoreIds());
                    } else {
                        $storeIds = $rule->getStoreIds();
                    }
                }
                $rule->applyToProduct2($product, $websiteIds, $storeIds);
            }
        } else {
            Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($product);
        }
        return $this;
    }
    /**
     * Apply catalog price rules to product on frontend
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function processFrontFinalPrice($observer)
    {
        $helper             = $this->getAdvancedPricingHelper();
        $currencyHelper     = $helper->getCoreHelper()->getCurrencyHelper();
        $event              = $observer->getEvent();
        $product            = $event->getProduct();
        $pId                = $product->getId();
        $storeId            = $product->getStoreId();
        if ($event->hasDate()) {
            $date = $event->getDate();
        } else {
            $date = Mage::app()->getLocale()->storeTimeStamp($storeId);
        }
        if ($event->hasWebsiteId()) {
            $wId = $event->getWebsiteId();
        } else {
            $wId = Mage::app()->getStore($storeId)->getWebsiteId();
        }
        if ($event->hasStoreId()) {
            $sId = $event->getStoreId();
        } else {
            $sId = Mage::app()->getStore($storeId)->getId();
        }
        if ($event->hasCustomerGroupId()) {
            $gId = $event->getCustomerGroupId();
        } elseif ($product->hasCustomerGroupId()) {
            $gId = $product->getCustomerGroupId();
        } else {
            $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        if ($event->hasCurrency()) {
            $curr = $event->getCurrency();
        } elseif ($product->hasCurrentCurrency()) {
            $curr = $product->getCurrentCurrency();
        } else {
            $curr = $currencyHelper->getCurrentCode();
        }
        $key = implode('|', array($date, $wId, $sId, $gId, $curr, $pId));
        if (!isset($this->_rulePrices[$key])) {
            $rulePrice = Mage::getResourceModel('catalogrule/rule')->getRulePrice2($date, $wId, $sId, $gId, $curr, $pId);
            $this->_rulePrices[$key] = $rulePrice;
        }
        if ($this->_rulePrices[$key]!==false) {
            $finalPrice = min($product->getData('final_price'), $this->_rulePrices[$key]);
            $product->setFinalPrice($finalPrice);
        }
        return $this;
    }
    /**
     * Apply catalog price rules to product in admin
     *
     * @param   Varien_Event_Observer $observer
     *
     * @return  Mage_CatalogRule_Model_Observer
     */
    public function processAdminFinalPrice($observer)
    {
        $helper     = $this->getAdvancedPricingHelper();
        $product    = $observer->getEvent()->getProduct();
        $storeId    = $product->getStoreId();
        $date       = Mage::app()->getLocale()->storeDate($storeId);
        $key        = false;
        if ($ruleData = Mage::registry('rule_data')) {
            $wId    = $ruleData->getWebsiteId();
            $sId    = $ruleData->getStoreId();
            $gId    = $ruleData->getCustomerGroupId();
            $curr   = $helper->getCoreHelper()->getStoreById($ruleData->getStoreId())->getCurrentCurrencyCode();
            $pId    = $product->getId();
            $key    = implode('|', array($date, $wId, $sId, $gId, $curr, $pId));
        } elseif (
            !is_null($product->getWebsiteId()) && !is_null($product->getStoreId()) && 
            !is_null($product->getCustomerGroupId())
        ) {
            $wId    = $product->getWebsiteId();
            $sId    = $product->getStoreId();
            $gId    = $product->getCustomerGroupId();
            $curr   = ($product->getCurrentCurrency()) ? 
                $product->getCurrentCurrency() : $helper->getCoreHelper()->getCurrencyHelper()->getCurrentCode();
            $pId    = $product->getId();
            $key    = implode('|', array($date, $wId, $sId, $gId, $curr, $pId));
        }
        if ($key) {
            if (!isset($this->_rulePrices[$key])) {
                $rulePrice = Mage::getResourceModel('catalogrule/rule')
                    ->getRulePrice2($date, $wId, $sId, $gId, $curr, $pId);
                $this->_rulePrices[$key] = $rulePrice;
            }
            if ($this->_rulePrices[$key] !== false) {
                $finalPrice = min($product->getData('final_price'), $this->_rulePrices[$key]);
                $product->setFinalPrice($finalPrice);
            }
        }
        return $this;
    }
    /**
     * Calculate minimal final price with catalog rule price
     *
     * @param Varien_Event_Observer $observer
     * 
     * @return Mage_CatalogRule_Model_Observer
     */
    public function prepareCatalogProductPriceIndexTable(Varien_Event_Observer $observer)
    {
        $event              = $observer->getEvent();
        $select             = $event->getSelect();
        $indexTable         = $event->getIndexTable();
        $entityId           = $event->getEntityId();
        $customerGroupId    = $event->getCustomerGroupId();
        $websiteId          = $event->getWebsiteId();
        $storeId            = $event->getStoreId();
        $currency           = $event->getCurrency();
        $websiteDate        = $event->getWebsiteDate();
        $updateFields       = $event->getUpdateFields();
        if ($entityId && $customerGroupId && $websiteId && $storeId && $currency && $websiteDate) {
            Mage::getSingleton('catalogrule/rule_product_price')->applyPriceRuleToIndexTable2(
                $select, $indexTable, $entityId, $customerGroupId, $currency, $websiteId, $storeId, $updateFields, $websiteDate
            );
        }
        return $this;
    }
    /**
     * Prepare catalog product collection prices
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self 
     */
    public function prepareCatalogProductCollectionPrices(Varien_Event_Observer $observer)
    {
        $helper         = $this->getAdvancedPricingHelper();
        $event          = $observer->getEvent();
        $collection     = $event->getCollection();
        $store          = Mage::app()->getStore($event->getStoreId());
        $websiteId      = $store->getWebsiteId();
        $storeId        = $store->getId();
        if ($event->hasCustomerGroupId()) {
            $groupId    = $event->getCustomerGroupId();
        } else {
            $session    = Mage::getSingleton('customer/session');
            if ($session->isLoggedIn()) {
                $groupId    = Mage::getSingleton('customer/session')->getCustomerGroupId();
            } else {
                $groupId    = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
            }
        }
        if ($event->hasCurrency()) {
            $currency   = $event->getCurrency();
        } else {
            $currency   = $helper->getCoreHelper()->getCurrencyHelper()->getCurrentCode();
        }
        if ($event->hasDate()) {
            $date = $event->getDate();
        } else {
            $date = Mage::app()->getLocale()->storeTimeStamp($store);
        }
        $productIds = array();
        foreach ($collection as $product) {
            $key = implode('|', array($date, $websiteId, $storeId, $groupId, $currency, $product->getId()));
            if (!isset($this->_rulePrices[$key])) {
                $productIds[] = $product->getId();
            }
        }
        if ($productIds) {
            $rulePrices = Mage::getResourceModel('catalogrule/rule')->getRulePrices2(
                $date, $websiteId, $storeId, $groupId, $currency, $productIds
            );
            foreach ($productIds as $productId) {
                $key = implode('|', array($date, $websiteId, $storeId, $groupId, $currency, $productId));
                $this->_rulePrices[$key] = isset($rulePrices[$productId]) ? $rulePrices[$productId] : false;
            }
        }
        return $this;
    }
    
    
    
    /**
     * Save compound discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function saveCompoundDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->saveCompoundDiscountAmount($observer->getEvent()->getRule());
        return $this;
    }
    /**
     * Load compound discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function loadCompoundDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->loadCompoundDiscountAmount($observer->getEvent()->getRule());
        return $this;
    }
    /**
     * Load collection compound discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function loadCollectionCompoundDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->loadCollectionCompoundDiscountAmount($observer->getEvent()->getCollection());
        return $this;
    }
    /**
     * Remove compound discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function removeCompoundDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->removeCompoundDiscountAmount($observer->getEvent()->getRule());
        return $this;
    }
    /**
     * Save compound sub discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function saveCompoundSubDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->saveCompoundSubDiscountAmount($observer->getEvent()->getRule());
        return $this;
    }
    /**
     * Load compound sub discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function loadCompoundSubDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->loadCompoundSubDiscountAmount($observer->getEvent()->getRule());
        return $this;
    }
    /**
     * Load collection compound sub discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function loadCollectionCompoundSubDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->loadCollectionCompoundSubDiscountAmount($observer->getEvent()->getCollection());
        return $this;
    }
    /**
     * Remove compound sub discount amount
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function removeCompoundSubDiscountAmount(Varien_Event_Observer $observer)
    {
        $this->getAdvancedPricingHelper()
            ->getCatalogRuleHelper()
            ->removeCompoundSubDiscountAmount($observer->getEvent()->getRule());
        return $this;
    }
}