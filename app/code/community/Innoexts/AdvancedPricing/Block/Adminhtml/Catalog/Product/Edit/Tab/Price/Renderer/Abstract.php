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
 * Price abstract renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */

abstract class Innoexts_AdvancedPricing_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Renderer_Abstract 
    extends Innoexts_Core_Block_Adminhtml_Widget_Form_Element_Renderer_Abstract 
{
    /**
     * Constructor
     */
    public function __construct() 
    {
        $this->setTemplate(
            'innoexts/advancedpricing/catalog/product/edit/tab/price/renderer/abstract.phtml'
        );
    }
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
     * Get product price helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalog_Product_Price
     */
    public function getProductPriceHelper()
    {
        return $this->getAdvancedPricingHelper()->getProductPriceHelper();
    }
    /**
     * Get product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
    /**
     * Get product identifier
     * 
     * @return int
     */
    public function getProductId()
    {
        return (int) $this->getProduct()->getId();
    }
    /**
     * Get store
     * 
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $storeId        = (int) $this->getRequest()->getParam('store', 0);
            $this->_store   = Mage::app()->getStore($storeId);
        }
        return $this->_store;
    }
    /**
     * Is product new
     * 
     * @return bool
     */
    public function isNew()
    {
        if ($this->getProductId()) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Check if global price scope is active
     * 
     * @return bool
     */
    public function isGlobalPriceScope()
    {
        return $this->getProductPriceHelper()->isGlobalScope();
    }
    /**
     * Check if website price scope is active
     * 
     * @return bool
     */
    public function isWebsitePriceScope()
    {
        return $this->getProductPriceHelper()->isWebsiteScope();
    }
    /**
     * Check if store price scope is active
     * 
     * @return bool
     */
    public function isStorePriceScope()
    {
        return $this->getProductPriceHelper()->isStoreScope();
    }
    /**
     * Get price scope string
     * 
     * @return string
     */
    public function getPriceScopeStr()
    {
        $scope = null;
        if ($this->isStorePriceScope()) {
            $scope      = '[STORE VIEW]';
        } else if ($this->isWebsitePriceScope()) {
            $scope      = '[WEBSITE]';
        } else {
            $scope      = '[GLOBAL]';
        }
        return $scope;
    }
    /**
     * Sort values function
     *
     * @param mixed $a
     * @param mixed $b
     * 
     * @return int
     */
    protected function sortValues($a, $b)
    {
        if ($a['currency'] != $b['currency']) {
            return $a['currency'] < $b['currency'] ? -1 : 1;
        }
        return 0;
    }
    /**
     * Get currency codes
     * 
     * @return array
     */
    public function getCurrencyCodes()
    {
        return $this->getAdvancedPricingHelper()
            ->getCoreHelper()
            ->getCurrencyHelper()
            ->getCodes();
    }
    /**
     * Get default price
     * 
     * @param mixed $currencyCode
     * 
     * @return float
     */
    abstract protected function getDefaultPrice($currencyCode);
    /**
     * Get values
     * 
     * @return array
     */
    public function getValues()
    {
        $helper             = $this->getAdvancedPricingHelper();
        $productHelper      = $helper->getProductHelper();
        $priceHelper        = $helper->getProductPriceHelper();
        $currencyHelper     = $helper->getCoreHelper()
            ->getCurrencyHelper();
        $currencyCodes      = $this->getCurrencyCodes();
        $values             = array();
        if (count($currencyCodes)) {
            $product            = $this->getProduct();
            $element            = $this->getElement();
            $readonly           = $element->getReadonly();
            $data               = $element->getValue();
            $currentStoreId     = $productHelper->getStoreId($product);
            $data               = (isset($data[$currentStoreId])) ? $data[$currentStoreId] : array();
            $store              = $this->getStore();
            $baseCurrency       = $store->getBaseCurrency();
            foreach ($currencyCodes as $currencyCode) {
                if (!$baseCurrency->getRate($currencyCode)) {
                    continue;
                }
                $value = array(
                    'currency' => $currencyCode, 
                );
                $defaultPrice = $this->getDefaultPrice($currencyCode);
                $defaultPrice = $priceHelper->escapedPrice($defaultPrice);
                if (isset($data[$currencyCode])) {
                    $value['price']             = $priceHelper->escapedPrice($data[$currencyCode]);
                    $value['default_price']     = $defaultPrice;
                    $value['use_default']       = 0;
                } else {
                    if (!is_null($defaultPrice)) {
                        $value['price']             = $defaultPrice;
                    } else {
                        $value['price']             = null;
                    }
                    $value['default_price']     = $defaultPrice;
                    $value['use_default']       = 1;
                }
                $value['readonly']      = $readonly;
                $value['rate']          = array();
                foreach ($currencyCodes as $currencyCode2) {
                    $value['rates'][$currencyCode2] = $currencyHelper->getRate($currencyCode2, $currencyCode);
                }
                array_push($values, $value);
            }
        }
        usort($values, array($this, 'sortValues'));
        return $values;
    }
}