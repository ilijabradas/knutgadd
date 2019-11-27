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
 * Product zone price tab
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Zone 
    extends Innoexts_Core_Block_Adminhtml_Widget_Grid_Editable_Area_Container 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
    /**
     * Grid blockyes
     * 
     * @var string
     */
    protected $_gridBlockType = 'advancedpricing/adminhtml_catalog_product_edit_tab_price_zone_grid';
    /**
     * Form block
     * 
     * @var string
     */
    protected $_formBlockType = 'advancedpricing/adminhtml_catalog_product_edit_tab_price_zone_form';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'Zone Discounts';
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productZonePriceTab');
        $this->setTemplate('innoexts/advancedpricing/catalog/product/edit/tab/price/zone.phtml');
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
    public function getProduct()
    {
        return Mage::registry('product');
    }
    /**
     * Retrieve Tab class
     * 
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTextHelper()->__($this->_title);
    }
    /**
     * Return Tab title
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTextHelper()->__($this->_title);
    }
    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
    /**
     * Check is allowed action
     * 
     * @param   string $action
     * 
     * @return  bool
     */
    protected function isAllowedAction($action)
    {
        return $this->getAdminSession()->isAllowed('catalog/products');
    }
    /**
     * Check if edit function enabled
     * 
     * @return bool
     */
    protected function canEdit()
    {
        $product = $this->getProduct();
        return ($this->isSaveAllowed() && $product->getId()) ? true : false;
    }
}