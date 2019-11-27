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
 * Product price indexer helper
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Helper_Catalog_Product_Price_Indexer 
    extends Innoexts_Core_Helper_Catalog_Product_Price_Indexer 
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
     * Get price helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalog_Product_Price
     */
    public function getPriceHelper()
    {
        return Mage::helper('advancedpricing/catalog_product_price');
    }
    /**
     * Get product helper
     * 
     * @return Innoexts_AdvancedPricing_Helper_Catalog_Product
     */
    public function getProductHelper()
    {
        return $this->getPriceHelper()
            ->getProductHelper();
    }
    /**
     * Get base currency expression
     * 
     * @param Zend_Db_Expr|string $websiteExpr
     * 
     * @return Zend_Db_Expr 
     */
    public function getBaseCurrencyExpr($websiteExpr)
    {
        return $this->getAdvancedPricingHelper()
            ->getCoreHelper()
            ->getCurrencyHelper()
            ->getBaseDbExpr($websiteExpr);
    }
    /**
     * Get currency expression
     * 
     * @param Zend_Db_Expr|string $websiteExpr
     * 
     * @return Zend_Db_Expr
     */
    public function getCurrencyExpr($websiteExpr)
    {
        return $this->getAdvancedPricingHelper()
            ->getCoreHelper()
            ->getCurrencyHelper()
            ->getDbExpr($websiteExpr);
    }
    /**
     * Get compound price join additional conditions
     * 
     * @param string $tableAlias
     * 
     * @return array
     */
    protected function getCompoundPriceJoinAdditionalConditions($tableAlias)
    {
        return array(
            "({$tableAlias}.currency = {$this->getCurrencyExpr('cw.website_id')})", 
            "({$tableAlias}.store_id = cs.store_id)", 
        );
    }
    /**
     * Get tier price join additional conditions
     * 
     * @param string $tableAlias
     * 
     * @return array
     */
    protected function getTierPriceJoinAdditionalConditions($tableAlias)
    {
        return array(
            "({$tableAlias}.store_id = cs.store_id)", 
            "({$tableAlias}.currency = {$this->getCurrencyExpr('cw.website_id')})", 
        );
    }
    /**
     * Get group price join additional conditions
     * 
     * @param string $tableAlias
     * 
     * @return array
     */
    protected function getGroupPriceJoinAdditionalConditions($tableAlias)
    {
        return array(
            "({$tableAlias}.store_id = cs.store_id)", 
            "({$tableAlias}.currency = {$this->getCurrencyExpr('cw.website_id')})", 
        );
    }
    /**
     * Add store join
     * 
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    protected function addStoreJoin($select)
    {
        $select->join(
                array('csg' => $this->getTable('core/store_group')), 
                'csg.website_id = cw.website_id', 
                array()
            )->join(
                array('cs' => $this->getTable('core/store')),
                'csg.group_id = cs.group_id AND cs.store_id != 0', 
                array()
            );
        return $this;
    }
    /**
     * Add currency rate to select
     * 
     * @param Varien_Db_Adapter_Interface $adapter
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    public function addCurrencyRateJoin($adapter, $select)
    {
        $tableAlias         = 'cr';
        $table              = $this->getTable('directory/currency_rate');
        $select->joinLeft(
            array($tableAlias => $table), 
            "({$tableAlias}.currency_from = {$this->getBaseCurrencyExpr('cw.website_id')})", 
            array()
        );
        return $this;
    }
    /**
     * Add price joins
     * 
     * @param Varien_Db_Adapter_Interface $adapter
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    public function addPriceJoins($adapter, $select)
    {
        $this->addCurrencyRateJoin($adapter, $select);
        parent::addPriceJoins($adapter, $select);
        return $this;
    }
    /**
     * Get final price select additional columns
     * 
     * @return array
     */
    protected function getFinalPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => $this->getCurrencyExpr('cw.website_id'), 
            'store_id' => new Zend_Db_Expr('cs.store_id'), 
        );
    }
    /**
     * Get prepare product index select event additional data
     * 
     * @return array
     */
    protected function getPrepareProductIndexSelectEventAdditionalData()
    {
        return array(
            'currency_field' => $this->getCurrencyExpr('cw.website_id'), 
        );
    }
    /**
     * Get prepare product index table event additional data
     * 
     * @return array
     */
    protected function getPrepareProductIndexTableEventAdditionalData()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Add option select store join
     * 
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    protected function addOptionSelectStoreJoin($select)
    {
        $select->join(
            array('cs' => $this->getTable('core/store')), 
            'cs.store_id = i.store_id', 
            array()
        );
        return $this;
    }
    /**
     * Get option type price select additional columns
     * 
     * @return array
     */
    protected function getOptionTypePriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Get option type price select group additional columns
     * 
     * @return array
     */
    protected function getOptionTypePriceSelectGroupAdditionalColumns()
    {
        return array('i.currency', 'i.store_id');
    }
    /**
     * Get option price select additional columns
     * 
     * @return array
     */
    protected function getOptionPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Get aggregated option price select additional columns
     * 
     * @return array
     */
    protected function getAggregatedOptionPriceSelectAdditionalColumns()
    {
        return array('currency', 'store_id');
    }
    /**
     * Get aggregated option price select group additional columns
     * 
     * @return array
     */
    protected function getAggregatedOptionPriceSelectGroupAdditionalColumns()
    {
        return array('currency', 'store_id');
    }
    /**
     * Get option final price select join additional conditions
     * 
     * @return array
     */
    protected function getOptionFinalPriceSelectJoinAdditionalConditions()
    {
        return array(
            '(i.currency = io.currency)', 
            '(i.store_id = io.store_id)', 
        );
    }
    /**
     * Get price select additional columns
     * 
     * @return array
     */
    protected function getPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'currency', 
            'store_id' => 'store_id', 
        );
    }
    /**
     * Get configurable option price select additional columns
     * 
     * @return array
     */
    protected function getConfigurableOptionPriceSelectAdditionalColumns()
    {
        if ($this->getVersionHelper()->isGe1600()) {
            return array(
                'currency' => 'i.currency', 
                'store_id' => 'i.store_id', 
            );
        } else {
            return array('i.currency', 'i.store_id');
        }
    }
    /**
     * Get configurable option price select group additional columns
     * 
     * @return array
     */
    protected function getConfigurableOptionPriceSelectGroupAdditionalColumns()
    {
        return array('i.currency', 'i.store_id');
    }
    /**
     * Get aggregated configurable option price select join additional columns
     * 
     * @return array
     */
    protected function getAggregatedConfigurableOptionPriceSelectJoinAdditionalColumns()
    {
        return array('currency', 'store_id');
    }
    /**
     * Get aggregated configurable option price select join group additional columns
     * 
     * @return array
     */
    protected function getAggregatedConfigurableOptionPriceSelectJoinGroupAdditionalColumns()
    {
        return array('currency', 'store_id');
    }
    /**
     * Get configurable option final price select join additional conditions
     * 
     * @return array
     */
    protected function getConfigurableOptionFinalPriceSelectJoinAdditionalConditions()
    {
        return array(
            '(i.currency = io.currency)', 
            '(i.store_id = io.store_id)', 
        );
    }
    /**
     * Get grouped product price select additional columns
     * 
     * @return array
     */
    protected function getGroupedProductPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Get grouped product price select group additional columns
     * 
     * @return array
     */
    protected function getGroupedProductPriceSelectGroupAdditionalColumns()
    {
        return array('i.currency', 'i.store_id');
    }
    /**
     * Get downloadable link price select additional columns
     * 
     * @return array
     */
    protected function getDownloadableLinkPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Get downloadable link price select group additional columns
     * 
     * @return array
     */
    protected function getDownloadableLinkPriceSelectGroupAdditionalColumns()
    {
        return array('i.currency', 'i.store_id');
    }
    /**
     * Get bundle price select additional columns
     * 
     * @return array
     */
    protected function getBundlePriceSelectAdditionalColumns()
    {
        return array(
            'currency' => $this->getCurrencyExpr('cw.website_id'), 
            'store_id' => new Zend_Db_Expr('cs.store_id'), 
        );
    }
    /**
     * Get bundle selection price select additional columns
     * 
     * @return array
     */
    protected function getBundleSelectionPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Get bundle selection price select index join additional conditions
     * 
     * @return array
     */
    protected function getBundleSelectionPriceSelectIndexJoinAdditionalConditions()
    {
        return array(
            '(i.currency = idx.currency)', 
            '(i.store_id = idx.store_id)'
        );
    }
    /**
     * Get bundle option price select additional columns
     * 
     * @return array
     */
    protected function getBundleOptionPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Get bundle option price select group additional columns
     * 
     * @return array
     */
    protected function getBundleOptionPriceSelectGroupAdditionalColumns()
    {
        return array('currency', 'store_id');
    }
    /**
     * Get bundle final price select group additional columns
     * 
     * @return array
     */
    protected function getBundleFinalPriceSelectGroupAdditionalColumns()
    {
        return array('io.currency', 'io.store_id');
    }
    /**
     * Get bundle final price select price join additional conditions
     * 
     * @return array
     */
    protected function getBundleFinalPriceSelectPriceJoinAdditionalConditions()
    {
        return array(
            '(i.currency = io.currency)', 
            '(i.store_id = io.store_id)'
        );
    }
    /**
     * Get bundle final price select additional columns
     * 
     * @return array
     */
    protected function getBundleFinalPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => 'i.currency', 
            'store_id' => 'i.store_id', 
        );
    }
    /**
     * Get tier price select additional columns
     * 
     * @return array
     */
    protected function getBundleTierPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => $this->getCurrencyExpr('cw.website_id'), 
            'store_id' => new Zend_Db_Expr('cs.store_id'), 
        );
    }
    /**
     * Get bundle tier price select group additional columns
     * 
     * @return array
     */
    protected function getBundleTierPriceSelectGroupAdditionalColumns()
    {
        return array($this->getCurrencyExpr('cw.website_id'), 'cs.store_id');
    }
    /**
     * Get bundle tier price select additional conditions
     * 
     * @return array
     */
    protected function getBundleTierPriceSelectAdditionalConditions()
    {
        return array('(cs.store_id != 0)');
    }
    /**
     * Add bundle tier price select additional joins
     * 
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    protected function addBundleTierPriceSelectAdditionalJoins($select)
    {
        $select->join(
                array('csg' => $this->getTable('core/store_group')), 
                'csg.website_id = cw.website_id', 
                array()
            )->joinLeft(
                array('cr' => $this->getTable('directory/currency_rate')), 
                implode(' AND ', array(
                    "(cr.currency_from = {$this->getBaseCurrencyExpr('cw.website_id')})", 
                    "((tp.currency IS NULL) OR (tp.currency = cr.currency_to))"
                )), array()
            )->join(
                array('cs' => $this->getTable('core/store')), 
                '(csg.group_id = cs.group_id) AND ((tp.store_id = 0) OR (tp.store_id = cs.store_id))', 
                array()
            );
        return $this;
    }
    /**
     * Get bundle group price select additional columns
     * 
     * @return array
     */
    protected function getBundleGroupPriceSelectAdditionalColumns()
    {
        return array(
            'currency' => $this->getCurrencyExpr('cw.website_id'), 
            'store_id' => new Zend_Db_Expr('cs.store_id'), 
        );
    }
    /**
     * Get bundle group price select group additional columns
     * 
     * @return array
     */
    protected function getBundleGroupPriceSelectGroupAdditionalColumns()
    {
        return array($this->getCurrencyExpr('cw.website_id'), 'cs.store_id');
    }
    /**
     * Get bundle group price select additional conditions
     * 
     * @return array
     */
    protected function getBundleGroupPriceSelectAdditionalConditions()
    {
        return array('(cs.store_id != 0)');
    }
    /**
     * Add bundle group price select additional joins
     * 
     * @param Zend_Db_Select $select
     * 
     * @return self
     */
    protected function addBundleGroupPriceSelectAdditionalJoins($select)
    {
        $select->join(
                array('csg' => $this->getTable('core/store_group')), 
                'csg.website_id = cw.website_id', 
                array()
            )->joinLeft(
                array('cr' => $this->getTable('directory/currency_rate')), 
                implode(' AND ', array(
                    "(cr.currency_from = {$this->getBaseCurrencyExpr('cw.website_id')})", 
                    "((gp.currency IS NULL) OR (gp.currency = cr.currency_to))"
                )), array()
            )->join(
                array('cs' => $this->getTable('core/store')), 
                '(csg.group_id = cs.group_id) AND ((gp.store_id = 0) OR (gp.store_id = cs.store_id))', 
                array()
            );
        return $this;
    }
    /**
     * Add price index filter
     * 
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * 
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addPriceIndexFilter($collection)
    {
        if (!$collection) {
            return null;
        }
        $select         = $collection->getSelect();
        $fromPart       = $select->getPart(Zend_Db_Select::FROM);
        $helper         = $this->getAdvancedPricingHelper();
        $currencyHelper = $helper->getCoreHelper()
            ->getCurrencyHelper();
        $connection     = $collection->getConnection();
        if (isset($fromPart['price_index'])) {
            $joinCond       = $fromPart['price_index']['joinCondition'];
            
            $currencyCode   = null;
            if (!$collection->getFlag('currency')) {
                $currencyCode   = $currencyHelper->getCurrentCode();
            } else {
                $currencyCode   = $collection->getFlag('currency');
            }
            $currencyCode   = $connection->quote($currencyCode);
            if (strpos($joinCond, 'price_index.currency') === false) {
                $joinCond .= " AND ((price_index.currency IS NULL) OR (price_index.currency = {$currencyCode}))";
            }
            
            $storeId        = null;
            if (!$collection->getFlag('store_id')) {
                $storeId        = $helper->getCoreHelper()->getCurrentStoreId();
            } else {
                $storeId        = $collection->getFlag('store_id');
            }
            $storeId        = $connection->quote($storeId);
            
            if (strpos($joinCond, 'price_index.store_id') === false) {
                if ($storeId) {
                    $joinCond .= " AND (price_index.store_id = {$storeId})";
                } else {
                    $joinCond .= " AND (price_index.store_id = 0)";
                }
            }
            
            $fromPart['price_index']['joinCondition'] = $joinCond;
            $select->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        return $collection;
    }
}