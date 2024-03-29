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
 * Downloadable products price indexer resource
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Mysql4_Downloadable_Indexer_Price 
    extends Mage_Downloadable_Model_Mysql4_Indexer_Price 
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
    protected function getVersionHelper()
    {
        return $this->getAdvancedPricingHelper()
            ->getVersionHelper();
    }
    /**
     * Get price indexer helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalog_Product_Price_Indexer
     */
    protected function getProductPriceIndexerHelper()
    {
        return $this->getAdvancedPricingHelper()
            ->getProductPriceIndexerHelper();
    }
    /**
     * Prepare final price data
     * 
     * @param int|array $entityIds
     * 
     * @return self
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $this->getProductPriceIndexerHelper()
            ->prepareFinalPriceData(
                $this->_getWriteAdapter(), 
                $this->_getDefaultFinalPriceTable(), 
                $this->getTypeId(), 
                $entityIds
            );
        return $this;
    }
    /**
     * Apply custom option
     * 
     * @return self
     */
    protected function _applyCustomOption()
    {
        $this->getProductPriceIndexerHelper()
            ->applyCustomOption(
                $this->_getWriteAdapter(), 
                $this->_getDefaultFinalPriceTable(), 
                $this->_getCustomOptionAggregateTable(), 
                $this->_getCustomOptionPriceTable(), 
                $this->useIdxTable()
            );
        return $this;
    }
    /**
     * Apply downloadable link
     *
     * @return self
     */
    protected function _applyDownloadableLink()
    {
        $this->getProductPriceIndexerHelper()
            ->applyDownloadableLink(
                $this->_getWriteAdapter(), 
                $this->_getDefaultFinalPriceTable(), 
                $this->_getDownloadableLinkPriceTable(), 
                $this->useIdxTable()
            );
        return $this;
    }
    /**
     * Mode price data to index table
     *
     * @return self
     */
    protected function _movePriceDataToIndexTable()
    {
        $this->getProductPriceIndexerHelper()
            ->movePriceDataToIndexTable(
                $this->_getWriteAdapter(), 
                $this->_getDefaultFinalPriceTable(), 
                $this->getIdxTable(), 
                $this->useIdxTable()
            );
        return $this;
    }
}