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
 * Catalog rule
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Catalogrule_Rule 
    extends Mage_CatalogRule_Model_Rule 
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
     * Validate rule data
     *
     * @param Varien_Object $object
     * 
     * @return bool|array - return true if validation passed successfully. Array with errors description otherwise
     */
    public function validateData(Varien_Object $object)
    {
        $helper     = $this->getAdvancedPricingHelper();
        $result     = parent::validateData($object);
        $result     = !empty($result) ? $result : array();
        if ($this->getVersionHelper()->isGe1700()) {
            if ($object->hasStoreIds()) {
                $storeIds   = $object->getStoreIds();
                if (empty($storeIds)) {
                    $result[]   = $helper->__('Stores must be specified.');
                }
            }
            if ($object->hasCurrencies()) {
                $currencies   = $object->getCurrencies();
                if (empty($currencies)) {
                    $result[]   = $helper->__('Currencies must be specified.');
                }
            }
        }
        return !empty($result) ? $result : true;
    }
    /**
     * Get rule associated store Ids
     * 
     * @return array
     */
    public function getStoreIds()
    {
        if ($this->getVersionHelper()->isGe1700()) {
            if (!$this->hasStoreIds()) {
                $storeIds = $this->_getResource()->getStoreIds($this->getId());
                $this->setData('store_ids', (array) $storeIds);
            }
            return $this->_getData('store_ids');
        } else {
            return parent::getStoreIds();
        }
    }
    /**
     * Get rule associated currencies
     * 
     * @return array
     */
    public function getCurrencies()
    {
        if ($this->getVersionHelper()->isGe1700()) {
            if (!$this->hasCurrencies()) {
                $currencies = $this->_getResource()->getCurrencies($this->getId());
                $this->setData('currencies', (array) $currencies);
            }
            return $this->_getData('currencies');
        } else {
            return parent::getCurrencies();
        }
    }
    /**
     * After load
     * 
     * @return Innoexts_AdvancedPricing_Model_Catalogrule_Rule
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if (!$this->getVersionHelper()->isGe1700()) {
            $storeIds = $this->_getData('store_ids');
            if (is_string($storeIds)) {
                $this->setStoreIds(explode(',', $storeIds));
            }
            $currencies = $this->_getData('currencies');
            if (is_string($currencies)) {
                $this->setCurrencies(explode(',', $currencies));
            }
        }
        return $this;
    }
    /**
     * Prepare data before saving
     *
     * @return Innoexts_AdvancedPricing_Model_Catalogrule_Rule
     */
    protected function _beforeSave()
    {
        if ($this->getVersionHelper()->isGe1700()) {
            if ($this->hasStoreIds()) {
                $storeIds = $this->getStoreIds();
                if (is_string($storeIds) && !empty($storeIds)) {
                    $this->setStoreIds(explode(',', $storeIds));
                }
            }
            if ($this->hasCurrencies()) {
                $currencies = $this->getCurrencies();
                if (is_string($currencies) && !empty($currencies)) {
                    $this->setCurrencies(explode(',', $currencies));
                }
            }
        } else {
            if (is_array($this->getStoreIds())) {
                $this->setStoreIds(join(',', $this->getStoreIds()));
            }
            if (is_array($this->getCurrencies())) {
                $this->setCurrencies(join(',', $this->getCurrencies()));
            }
        }
        parent::_beforeSave();
        return $this;
    }
    /**
     * Process rule related data after rule save
     *
     * @return Innoexts_AdvancedPricing_Model_Catalogrule_Rule
     */
    protected function _afterSave()
    {
        if ($this->getVersionHelper()->isGe1700()) {
            parent::_afterSave();
        } else {
            Mage_Core_Model_Abstract::_afterSave();
            $this->_getResource()->updateRuleProductData($this);
        }
        return $this;
    }
    /**
     * Apply rule to product
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param array|null $websiteIds
     * @param array|null $storeIds
     *
     * @return void
     */
    public function applyToProduct2($product, $websiteIds = null, $storeIds = null)
    {
        if (is_numeric($product)) {
            if ($this->getVersionHelper()->isGe1800()) {
                $product = $this->_factory->getModel('catalog/product')->load($product);
            } else {
                $product = Mage::getModel('catalog/product')->load($product);
            }
        }
        if (is_null($websiteIds)) {
            if ($this->getVersionHelper()->isGe1700()) {
                $websiteIds = $this->getWebsiteIds();
            } else {
                $websiteIds = explode(',', $this->getWebsiteIds());
            }
        }
        if (is_null($storeIds)) {
            if ($this->getVersionHelper()->isGe1700()) {
                $storeIds = $this->getStoreIds();
            } else {
                $storeIds = explode(',', $this->getStoreIds());
            }
        }
        $this->getResource()->applyToProduct2($this, $product, $websiteIds, $storeIds);
        
        if ($this->getVersionHelper()->isGe1800()) {
            $this->getResource()->applyAllRules($product);
            $this->_invalidateCache();
        }
    }
    /**
     * Apply all price rules to product
     *
     * @param  int|Mage_Catalog_Model_Product $product
     * @return Mage_CatalogRule_Model_Rule
     */
    public function applyAllRulesToProduct($product)
    {
        if ($this->getVersionHelper()->isGe1800()) {
            if (is_numeric($product)) {
                $product = Mage::getModel('catalog/product')->load($product);
            }
            $productWebsiteIds = $product->getWebsiteIds();
            $rules = Mage::getModel('catalogrule/rule')
                ->getCollection()
                ->addFieldToFilter('is_active', 1);
            foreach ($rules as $rule) {
                $websiteIds     = array_intersect($productWebsiteIds, $rule->getWebsiteIds());
                $storeIds       = $rule->getStoreIds();
                $this->getResource()->applyToProduct2($rule, $product, $websiteIds, $storeIds);
            }
            $this->getResource()->applyAllRules($product);
            $this->_invalidateCache();
            Mage::getSingleton('index/indexer')->processEntityAction(
                new Varien_Object(array('id' => $product->getId())), 
                Mage_Catalog_Model_Product::ENTITY, 
                Mage_Catalog_Model_Product_Indexer_Price::EVENT_TYPE_REINDEX_PRICE
            );
            return $this;
        } else {
            $this->_getResource()->applyAllRulesForDateRange(NULL, NULL, $product);
            $this->_invalidateCache();
            
            if ($this->getVersionHelper()->isGe1600()) {
                if ($product instanceof Mage_Catalog_Model_Product) {
                    $productId = $product->getId();
                } else {
                    $productId = $product;
                }
                if ($productId) {
                    
                    if ($this->getVersionHelper()->isGe1620()) {
                        Mage::getSingleton('index/indexer')->processEntityAction(
                            new Varien_Object(array('id' => $productId)),
                            Mage_Catalog_Model_Product::ENTITY,
                            Mage_Catalog_Model_Product_Indexer_Price::EVENT_TYPE_REINDEX_PRICE
                        );
                    } else {
                        Mage::getResourceSingleton('catalog/product_indexer_price')->reindexProductIds(array($productId));
                    }
                    
                }
            } else {
                $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_price');
                if ($indexProcess) {
                    $indexProcess->reindexAll();
                }
            }
            
            return $this;
        }
    }
    /**
     * Calculate price using catalog price rule of product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * @return float|null
     */
    public function calcProductPriceRule(Mage_Catalog_Model_Product $product, $price)
    {
        $helper         = $this->getAdvancedPricingHelper();
        $priceRules     = null;
        $productId      = $product->getId();
        $storeId        = $product->getStoreId();
        $websiteId      = Mage::app()->getStore($storeId)->getWebsiteId();
        if ($product->hasCustomerGroupId()) {
            $customerGroupId = $product->getCustomerGroupId();
        } else {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        if ($product->hasCurrentCurrency()) {
            $currency        = $product->getCurrentCurrency();
        } else {
            $currency        = $helper->getCoreHelper()->getCurrencyHelper()->getCurrentCode();
        }
        $dateTs     = Mage::app()->getLocale()->storeTimeStamp($storeId);
        $cacheKey   = date('Y-m-d', $dateTs).implode('|', array(
            $websiteId, $storeId, $customerGroupId, $currency, $productId, $price
        ));
        if (!array_key_exists($cacheKey, self::$_priceRulesData)) {
            $rulesData = $this->_getResource()->getRulesFromProduct2(
                $dateTs, $websiteId, $storeId, $customerGroupId, $currency, $productId
            );
            if ($rulesData) {
                foreach ($rulesData as $ruleData) {
                    if ($this->getVersionHelper()->isGe1610() && $product->getParentId()) {
                        if (
                            ($this->getVersionHelper()->isGe1700() && !empty($ruleData['sub_simple_action'])) || 
                            (!$this->getVersionHelper()->isGe1700() && $ruleData['sub_is_enable'])
                        ) {
                            $priceRules = Mage::helper('catalogrule')->calcPriceRule(
                                $ruleData['sub_simple_action'],
                                $ruleData['sub_discount_amount'],
                                $priceRules ? $priceRules : $price
                            );
                        } else {
                            $priceRules = $price;
                        }
                        if (
                            ($this->getVersionHelper()->isGe1700() && $ruleData['action_stop']) || 
                            (!$this->getVersionHelper()->isGe1700() && $ruleData['stop_rules_processing'])
                        ) {
                            break;
                        }
                    } else {
                        if ($this->getVersionHelper()->isGe1700()) {
                            $priceRules = Mage::helper('catalogrule')->calcPriceRule(
                                $ruleData['action_operator'],
                                $ruleData['action_amount'],
                                $priceRules ? $priceRules : $price
                            );
                        } else {
                            $priceRules = Mage::helper('catalogrule')->calcPriceRule(
                                $ruleData['simple_action'],
                                $ruleData['discount_amount'],
                                $priceRules ? $priceRules :$price
                            );
                        }
                        if (
                            ($this->getVersionHelper()->isGe1700() && $ruleData['action_stop']) || 
                            (!$this->getVersionHelper()->isGe1700() && $ruleData['stop_rules_processing'])
                        ) {
                            break;
                        }
                    }
                }
                return self::$_priceRulesData[$cacheKey] = $priceRules;
            } else {
                self::$_priceRulesData[$cacheKey] = null;
            }
        } else {
            return self::$_priceRulesData[$cacheKey];
        }
        return null;
    }
}