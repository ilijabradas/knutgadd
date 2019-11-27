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
 * Product attributes tab
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Block_Bundle_Adminhtml_Catalog_Product_Edit_Tab_Attributes 
    extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes 
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
     * Get product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
    /**
     * Prepare form before rendering HTML
     * 
     * @return self
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $group          = $this->getGroup();
        if (!$group) {
            return $this;
        }
        $form           = $this->getForm();
        $fieldset       = $form->getElement('group_fields'.$group->getId());
        if (!$fieldset) {
            return $this;
        }
        $helper         = $this->getAdvancedPricingHelper();
        $product        = $this->getProduct();
        if ($form->getElement('price')) {
            $fieldset->addField('compound_prices', 'text', array(
                'name'      => 'compound_prices', 
                'label'     => $helper->__('Compound Price'), 
                'title'     => $helper->__('Compound Price'), 
                'required'  => false, 
                'value'     => $product->getCompoundPrices(), 
            ), 'price')->setRenderer($this->getLayout()->createBlock(
                'advancedpricing/adminhtml_catalog_product_edit_tab_price_compound_renderer'
            ));
        }
        return $this;
    }
}