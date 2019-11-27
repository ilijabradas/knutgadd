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
 * Catalog rule main tab
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Block_Adminhtml_Promo_Catalog_Edit_Tab_Main 
    extends Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main 
{
    /**
     * Get currency pricing helper
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
    public function getVersionHelper()
    {
        return $this->getAdvancedPricingHelper()->getVersionHelper();
    }
    /**
     * Get catalog rule
     * 
     * @return Mage_CatalogRule_Model_Rule
     */
    protected function getCatalogRule()
    {
        return Mage::registry('current_promo_catalog_rule');
    }
    /**
     * Get store values
     * 
     * @return array
     */
    protected function getStoreValues()
    {
        if ($this->getVersionHelper()->isGe1700()) {
            return Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm();
        } else {
            return Mage::getSingleton('adminhtml/system_config_source_store')->toOptionArray();
        }
    }
    /**
     * Get currency values
     * 
     * @return array
     */
    protected function getCurrencyValues()
    {
        $helper = $this->getAdvancedPricingHelper();
        $options = array();
        foreach ($helper->getCoreHelper()->getCurrencyHelper()->getCodes() as $currencyCode) {
            array_push($options, array(
                'label' => $currencyCode, 
                'value' => $currencyCode
            ));
        }
        return $options;
    }
    /**
     * Get store renderer
     * 
     * @return Innoexts_AdvancedPricing_Block_Adminhtml_Store_Switcher_Form_Renderer_Fieldset_Element
     */
    protected function getStoreRenderer()
    {
        return $this->getLayout()
            ->createBlock('advancedpricing/adminhtml_store_switcher_form_renderer_fieldset_element');
    }
    /**
     * Prepare form
     * 
     * @return self
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form           = $this->getForm();
        if (!$form) {
            return $this;
        }
        $fieldset       = $form->getElement('base_fieldset');
        if (!$fieldset) {
            return $this;
        }
        $helper         = $this->getAdvancedPricingHelper();
        $catalogRule    = $this->getCatalogRule();
        $isReadonly     = $catalogRule->isReadonly();
        
        if ($helper->getCoreHelper()->isSingleStoreMode()) {
            $storeId        = $helper->getCoreHelper()->getStoreById(true)->getId();
            $fieldset->addField('store_ids', 'hidden', array(
                'name'          => 'store_ids[]', 
                'value'         => $storeId, 
                'readonly'      => $isReadonly, 
                'disabled'      => $isReadonly, 
            ), 'website_ids');
            $catalogRule->setStoreIds($storeId);
        } else {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name'          => 'store_ids[]', 
                'label'         => $helper->__('Stores'), 
                'title'         => $helper->__('Stores'), 
                'required'      => true, 
                'value'         => $catalogRule->getStoreIds(), 
                'values'        => $this->getStoreValues(), 
                'readonly'      => $isReadonly, 
                'disabled'      => $isReadonly, 
            ), 'website_ids')->setRenderer($this->getStoreRenderer());
        }
        $fieldset->addField('currencies', 'multiselect', array(
            'name'          => 'currencies[]', 
            'label'         => $helper->__('Currencies'), 
            'title'         => $helper->__('Currencies'), 
            'required'      => true, 
            'value'         => $catalogRule->getCurrencies(), 
            'values'        => $this->getCurrencyValues(), 
            'readonly'      => $isReadonly, 
            'disabled'      => $isReadonly, 
        ), 'customer_group_ids');
        
        return $this;
    }
}