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
 * Product collection
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Mysql4_Catalog_Product_Collection 
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection 
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
     * Add tier price data to loaded items
     *
     * @return self
     */
    public function addTierPriceData()
    {
        $this->getAdvancedPricingHelper()
            ->getProductPriceHelper()
            ->loadCollectionTierPrice($this);
        return $this;
    }
}