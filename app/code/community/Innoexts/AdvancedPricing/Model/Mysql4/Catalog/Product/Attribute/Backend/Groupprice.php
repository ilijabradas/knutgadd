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
 * Product group price backend attribute resource
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Mysql4_Catalog_Product_Attribute_Backend_Groupprice 
    extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice 
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
     * Load tier prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @param string $storeId
     * @param string $currency
     * 
     * @return array
     */
    public function loadPriceData2($productId, $websiteId = null, $storeId = null, $currency = null)
    {
        $adapter = $this->_getReadAdapter();
        $columns = array(
            'price_id'      => $this->getIdFieldName(), 
            'website_id'    => 'website_id', 
            'store_id'      => 'store_id', 
            'all_groups'    => 'all_groups', 
            'cust_group'    => 'customer_group_id', 
            'price'         => 'value', 
            'currency'      => 'currency', 
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $productId);
        if (!is_null($websiteId)) {
            if ($websiteId == '0') {
                $select->where('website_id = ?', $websiteId);
            } else {
                $select->where('website_id IN(?)', array(0, $websiteId));
            }
        }
        if (!is_null($storeId)) {
            if ($storeId == '0') {
                $select->where('store_id = ?', $storeId);
            } else {
                $select->where('store_id IN(?)', array(0, $storeId));
            }
        }
        if (!is_null($currency)) {
            if ($currency == '') {
                $select->where("(currency IS NULL) OR (currency = '')", $currency);
            } else {
                $select->where("(currency = ?) OR (currency IS NULL) OR (currency = '')", $currency);
            }
        }
        return $adapter->fetchAll($select);
    }
    /**
     * Delete tier prices
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $storeId
     * @param int $priceId
     * 
     * @return int number of affected rows
     */
    public function deletePriceData2($productId, $websiteId = null, $storeId = null, $priceId = null)
    {
        $adapter = $this->_getWriteAdapter();
        $conds   = array(
            $adapter->quoteInto('entity_id = ?', $productId)
        );
        if (!is_null($websiteId)) {
            $conds[] = $adapter->quoteInto('website_id = ?', $websiteId);
        }
        if (!is_null($storeId)) {
            $conds[] = $adapter->quoteInto('store_id = ?', $storeId);
        }
        if (!is_null($priceId)) {
            $conds[] = $adapter->quoteInto($this->getIdFieldName() . ' = ?', $priceId);
        }
        $where = implode(' AND ', $conds);
        return $adapter->delete($this->getMainTable(), $where);
    }
}