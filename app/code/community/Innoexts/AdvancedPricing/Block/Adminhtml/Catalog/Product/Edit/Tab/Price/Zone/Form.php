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
 * Zone price form
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Zone_Form 
    extends Innoexts_Core_Block_Adminhtml_Widget_Grid_Editable_Area_Form 
{
    /**
     * Form field name suffix
     * 
     * @var string
     */
    protected $_formFieldNameSuffix = 'product_zone_price';
    /**
     * Form HTML identifier prefix
     * 
     * @var string
     */
    protected $_formHtmlIdPrefix = 'product_zone_price_';
    /**
     * Form field set identifier
     * 
     * @var string
     */
    protected $_formFieldsetId = 'product_zone_price_fieldset';
    /**
     * Form field set legend
     * 
     * @var string
     */
    protected $_formFieldsetLegend = 'Discount';
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productZonePriceTabForm');
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
     * Retrieve save URL
     * 
     * @return string
     */
    public function getSaveUrl()
    {
        $model = $this->getModel();
        $params = array('id' => $this->getProduct()->getId());
        if ($model->getId()) {
            $params['zone_price_id'] = $model->getId();
        }
        return $this->getUrl('*/catalog_product_zone/savePrice', $params);
    }
    /**
     * Get price type values
     * 
     * @return array
     */
    protected function getPriceTypeValues()
    {
        $helper = $this->getTextHelper();
        return array(
            array('value' => 'fixed', 'label' => $helper->__('Fixed'), ), 
            array('value' => 'percent', 'label' => $helper->__('Percent'), ), 
        );
    }
    /**
     * Get is zip range options
     * 
     * @return array
     */
    protected function getIsZipRangeOptions()
    {
        $helper = $this->getTextHelper();
        return array(
            '0' => $helper->__('No'), 
            '1' => $helper->__('Yes'), 
        );
    }
    /**
     * Prepare form before rendering HTML
     *
     * @return self
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $fieldset = $this->getFieldset();
        if ($fieldset) {
            $helper = $this->getTextHelper();
            $model = $this->getModel();
            $isElementDisabled = !$this->isSaveAllowed();
            $fieldset->addField('zone_price_id', 'hidden', array(
                'name'       => 'zone_price_id', 
                'value'      => $model->getId(), 
                'default'    => '', 
            ));
            $fieldset->addField('is_zip_range', 'select', array(
                'name'       => 'is_zip_range', 
                'label'      => $helper->__('Zip/Postal Code is Range'), 
                'title'      => $helper->__('Zip/Postal Code is Range'), 
                'required'   => false, 
                'options'    => $this->getIsZipRangeOptions(), 
                'value'      => (($model->getIsZipRange()) ? '1' : '0'), 
                'default'    => '0', 
                'disabled'   => $isElementDisabled, 
            ), 'region_id');
            $fieldset->removeField('zip');
            $fieldset->addField('zip', 'text', array(
                'name'       => 'zip', 
                'label'      => $helper->__('Zip/Postal Code'), 
                'title'      => $helper->__('Zip/Postal Code'), 
                'note'       => $helper->__('\'*\' - matches any.'), 
                'required'   => false, 
                'value'		 => $this->getZipValue(), 
                'default'    => '', 
                'disabled'   => $isElementDisabled, 
            ), 'is_zip_range');
            $fieldset->addField('from_zip', 'text', array(
                'name'       => 'from_zip', 
                'label'      => $helper->__('Zip/Postal Code From'), 
                'title'      => $helper->__('Zip/Postal Code From'), 
                'required'   => true, 
                'value'      => $model->getFromZip(), 
                'disabled'   => $isElementDisabled, 
                'class'      => 'validate-digits', 
            ), 'zip');
            $fieldset->addField('to_zip', 'text', array(
                'name'       => 'to_zip', 
                'label'      => $helper->__('Zip/Postal Code To'), 
                'title'      => $helper->__('Zip/Postal Code To'), 
                'required'   => true, 
                'value'      => $model->getToZip(), 
                'disabled'   => $isElementDisabled, 
                'class'      => 'validate-digits', 
            ), 'from_zip');
            $fieldset->addField('price', 'text', array(
                'name'       => 'price', 
                'label'      => $helper->__('Amount'), 
                'title'      => $helper->__('Amount'), 
                'required'   => true, 
                'value'      => floatval($model->getPrice()), 
                'default'    => '0', 
                'disabled'   => $isElementDisabled, 
            ));
            $fieldset->addField('price_type', 'select', array(
                'name'       => 'price_type', 
                'label'      => $helper->__('Apply'), 
                'title'      => $helper->__('Apply'), 
                'required'   => true, 
                'value'      => $model->getPriceType(), 
                'default'    => 'fixed', 
                'values'     => $this->getPriceTypeValues(), 
                'disabled'   => $isElementDisabled, 
            ));
            $fieldset->addField('submit_button', 'note', array(
                'text' => $this->getButtonHtml(
                    $helper->__('Submit'), 
                    $this->getJsObjectName().'.submit(\''.$this->getSaveUrl().'\');', 
                    'save'
                )
            ));
        }
        return $this;
    }
}