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
 * Product zone price collection
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Mysql4_Catalog_Product_Zone_Price_Collection 
    extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    /**
     * directory/country table name
     * 
     * @var string
     */
    protected $_countryTable;
    /**
     * directory/country_region table name
     *
     * @var string
     */
    protected $_regionTable;
    /**
     * Constructor
     */
    protected function _construct() {
        $this->_init('catalog/product_zone_price');
        $this->_countryTable = $this->getTable('directory/country');
        $this->_regionTable = $this->getTable('directory/country_region');
    }
    /**
     * Initialize select, add country iso3 code and region name
     *
     * @return void
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(array('country_table' => $this->_countryTable), 
                'country_table.country_id = main_table.country_id', array('country' => 'iso2_code'))
            ->joinLeft(array('region_table' => $this->_regionTable), 
                'region_table.region_id = main_table.region_id', array('region' => 'code'));
    }
    /**
     * Set product filter to collection
     *
     * @param int $productId
     * 
     * @return self
     */
    public function setProductFilter($productId)
    {
        return $this->addFieldToFilter('product_id', $productId);
    }
    /**
     * Set products filter to collection
     *
     * @param array $products
     * 
     * @return self
     */
    public function setProductsFilter($products)
    {
        $productIds = array();
        foreach ($products as $product) {
            if ($product instanceof Mage_Catalog_Model_Product) {
                $productIds[] = $product->getId();
            } else {
                $productIds[] = $product;
            }
        }
        if (empty($productIds)) {
            $productIds[] = false;
            $this->_setIsLoaded(true);
        }
        $this->addFieldToFilter('main_table.product_id', array('in' => $productIds));
        return $this;
    }
    /**
     * Set address filter to collection
     *
     * @param Varien_Object $address
     * 
     * @return self
     */
    public function setAddressFilter($address)
    {
        $select = $this->getConnection()->select();
        $table = 'pzp';
        $productId      = $table.'.product_id';
        $countryId      = $table.'.country_id';
        $regionId       = $table.'.region_id';
        $isZipRange     = $table.'.is_zip_range';
        $zip            = $table.'.zip';
        $fromZip        = $table.'.from_zip';
        $toZip          = $table.'.to_zip';
        $countryIdOrder = "{$countryId} DESC";
        $regionIdOrder  = "{$regionId} DESC";
        $zipOrder       = "(IF ({$isZipRange} = '0', IF (({$zip} IS NULL) OR ({$zip} = ''), 3, 1), 2)) ASC";
        $select->from(array($table => $this->getMainTable()), 'zone_price_id')
            ->order(array($countryIdOrder, $regionIdOrder, $zipOrder))
            ->limit(1);
        $countryIdWhere       = "{$countryId} = :country_id";
        $countryIdEmptyWhere  = "{$countryId} = '0'";
        $regionIdWhere        = "{$regionId} = :region_id";
        $regionIdEmptyWhere   = "{$regionId} = '0'";
        $zipWhere             = "(IF ({$isZipRange} <> '0', (:postcode >= {$fromZip}) AND (:postcode <= {$toZip}), {$zip} = :postcode))";
        $zipEmptyWhere        = "(({$isZipRange} = '0') AND (({$zip} IS NULL) OR ({$zip} = '')))";
        $where = '('.implode(') OR (', array(
            "{$countryIdWhere} AND {$regionIdWhere} AND {$zipWhere}", 
            "{$countryIdWhere} AND {$regionIdWhere} AND {$zipEmptyWhere}", 
            "{$countryIdWhere} AND {$regionIdEmptyWhere} AND {$zipEmptyWhere}", 
            "{$countryIdWhere} AND {$regionIdEmptyWhere} AND {$zipWhere}", 
            "{$countryIdEmptyWhere} AND {$regionIdEmptyWhere} AND {$zipEmptyWhere}", 
        )).')';
        $select->where("({$productId} = main_table.product_id) AND (".$where.")");
        $this->getSelect()->where('main_table.zone_price_id = ('.$select->assemble().')');
        $this->addBindParam(':country_id', $address->getCountryId());
        $this->addBindParam(':region_id', $address->getRegionId());
        $this->addBindParam(':postcode', $address->getPostcode());
        return $this;
    }
}