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
 * Advanced pricing helper
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Helper_Data 
    extends Innoexts_Core_Helper_Abstract 
{
    /**
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    public function getVersionHelper()
    {
        return $this->getCoreHelper()->getVersionHelper();
    }
    /**
     * Get customer locator helper
     * 
     * @return Innoexts_CustomerLocator_Helper_Data
     */
    public function getCustomerLocatorHelper()
    {
        return Mage::helper('customerlocator');
    }
    /**
     * Get catalog rule helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalogrule_Rule
     */
    public function getCatalogRuleHelper()
    {
        return Mage::helper('advancedpricing/catalogrule_rule');
    }
    /**
     * Get product helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalog_Product
     */
    public function getProductHelper()
    {
        return Mage::helper('advancedpricing/catalog_product');
    }
    /**
     * Get product price helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalog_Product_Price
     */
    public function getProductPriceHelper()
    {
        return $this->getProductHelper()->getPriceHelper();
    }
    /**
     * Get product price indexer helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalog_Product_Price_Indexer
     */
    public function getProductPriceIndexerHelper()
    {
        return $this->getProductPriceHelper()->getIndexerHelper();
    }
    /**
     * Get config
     * 
     * @return Innoexts_AdvancedPricing_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('advancedpricing/config');
    }
}