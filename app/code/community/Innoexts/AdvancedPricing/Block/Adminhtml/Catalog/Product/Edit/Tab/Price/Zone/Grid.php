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
 * Zone price grid
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Zone_Grid 
    extends Innoexts_Core_Block_Adminhtml_Widget_Grid_Editable_Area_Grid 
{
    /**
     * Add button label
     * 
     * @var string
     */
    protected $_addButtonLabel = 'Add Discount';
    /**
     * Form js object name
     * 
     * @var string
     */
    protected $_formJsObjectName = 'productZonePriceTabFormJsObject';
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productZonePriceGrid');
        $this->setDefaultSort('zone_price_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }
    /**
     * Get text helper
     * 
     * @return Varien_Object
     */
    public function getTextHelper()
    {
        return Mage::helper('advancedpricing');
    }
    /**
     * Retrieve registered product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct()
    {
        return Mage::registry('product');
    }
    /**
     * Prepare collection
     * 
     * @return Innoexts_AdvancedPricing_Model_Mysql4_Catalog_Product_Zone_Price_Collection
     */
    protected function __prepareCollection()
    {
        $product = $this->getProduct();
        $collection = Mage::getModel('catalog/product_zone_price')->getCollection();
        $collection->setProductFilter($product->getId());
        return $collection;
    }
    /**
     * Get store
     * 
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    /**
     * Retrieve price type options
     * 
     * @return array
     */
    protected function getPriceTypeOptions()
    {
        $helper = $this->getTextHelper();
        return array(
            'fixed'     => $helper->__('Fixed'), 
            'percent'   => $helper->__('Percent'), 
        );
    }
    /**
     * Add columns to grid
     *
     * @return self
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $helper = $this->getTextHelper();
        $this->addColumn('price', array(
            'header'        => $helper->__('Amount'), 
            'align'         => 'left', 
            'index'         => 'price', 
            'type'          => 'number', 
        ));
        $this->addColumn('price_type', array(
            'header'        => $helper->__('Apply'), 
            'align'         => 'left', 
            'index'         => 'price_type', 
            'filter_index'  => 'main_table.price_type', 
            'type'          => 'options', 
            'options'       => $this->getPriceTypeOptions(), 
        ));
        $this->addColumn('action', array(
            'header'        => $helper->__('Action'), 
            'width'         => '90px', 
            'type'          => 'action', 
            'getter'        => 'getId', 
            'actions'       => array(
                array(
                    'name'      => 'edit', 
                    'caption'   => $helper->__('Edit'), 
                    'url'       => array('base' => '*/catalog_product_zone/editPrice', 'params' => $this->getRowUrlParameters()), 
                    'field'     => 'zone_price_id'
                ), 
                array(
                    'name'      => 'delete', 
                    'caption'   => $helper->__('Delete'), 
                    'url'       => array('base' => '*/catalog_product_zone/deletePrice', 'params' => $this->getRowUrlParameters()), 
                    'field'     => 'zone_price_id', 
                    'confirm'   => $helper->__('Are you sure you want to delete discount?')
                )
            ), 
            'filter'        => false, 
            'sortable'      => false, 
        ));
        $this->sortColumnsByOrder();
        return $this;
    }
    /**
     * Retrieve grid URL
     * 
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url') ? 
            $this->getData('grid_url') : 
            $this->getUrl('*/catalog_product_zone/priceGrid', array('_current' => true));
    }
    /**
     * Get row URL parameters
     * 
     * @param Varien_Object|null $row
     * 
     * @return array
     */
    protected function getRowUrlParameters($row = null)
    {
        $params = array('id' => $this->getProduct()->getId());
        if ($row) {
            $params['zone_price_id'] = $row->getId();
        }
        return $params;
    }
    /**
     * Get row URL
     * 
     * @param Varien_Object $row
     * 
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_zone/editPrice', $this->getRowUrlParameters($row));
    }
}