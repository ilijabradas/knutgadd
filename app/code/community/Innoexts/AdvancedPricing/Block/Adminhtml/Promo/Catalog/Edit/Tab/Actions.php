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
 * Catalog rule actions tab
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Block_Adminhtml_Promo_Catalog_Edit_Tab_Actions 
    extends Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Actions 
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
        return $this->getAdvancedPricingHelper()
            ->getVersionHelper();
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
        $fieldset       = $form->getElement('action_fieldset');
        if (!$fieldset) {
            return $this;
        }
        $helper         = $this->getAdvancedPricingHelper();
        $catalogRule    = $this->getCatalogRule();
        $isReadonly     = $catalogRule->isReadonly();
        $layout         = $this->getLayout();
        
        $fieldset->addField('compound_discount_amounts', 'text', array(
            'name'          => 'compound_discount_amounts', 
            'required'      => false, 
            'title'         => $helper->__('Compound Discount Amount'), 
            'label'         => $helper->__('Compound Discount Amount'), 
            'value'         => $catalogRule->getCompoundDiscountAmounts(), 
            'readonly'      => $isReadonly, 
            'disabled'      => $isReadonly, 
        ), 'discount_amount')->setRenderer(
            $layout->createBlock(
                'advancedpricing/adminhtml_promo_catalog_edit_tab_actions_compound_discountamount_renderer'
            )
        );
        
        if ($this->getVersionHelper()->isGe1610()) {
            $fieldset->addField('compound_sub_discount_amounts', 'text', array(
                'name'          => 'compound_sub_discount_amounts', 
                'required'      => false, 
                'title'         => $helper->__('Compound Discount Amount'), 
                'label'         => $helper->__('Compound Discount Amount'), 
                'value'         => $catalogRule->getCompoundSubDiscountAmounts(), 
                'readonly'      => $isReadonly, 
                'disabled'      => $isReadonly, 
            ), 'sub_discount_amount')->setRenderer(
                $layout->createBlock(
                    'advancedpricing/adminhtml_promo_catalog_edit_tab_actions_compound_subdiscountamount_renderer'
                )
            );
        }
        
        return $this;
    }
}