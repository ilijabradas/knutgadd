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
 * Catalog observer
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Catalog_Product_Observer 
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
     * Add tabs
     * 
     * @param Varien_Event_Observer $observer
     * 
     * @return self
     */
    public function addTabs(Varien_Event_Observer $observer)
    {
        $helper         = $this->getAdvancedPricingHelper();
        $block          = $observer->getEvent()->getBlock();
        if (!($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs)) {
            return $this;
        }
        $request        = $helper->getCoreHelper()
            ->getRequest();
        if (($request->getActionName() != 'edit') && !$request->getParam('type')) {
            return $this;
        }
        
        $after          = null;
        $tabBlock       = $block->getLayout()->createBlock(
            'advancedpricing/adminhtml_catalog_product_edit_tab_price_zone'
        );
        $product        = $tabBlock->getProduct();
        if ($product && $product->getAttributeSetId()) {
            $attributeSetId     = $product->getAttributeSetId();
            $groupCollection    = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($attributeSetId);
            foreach ($groupCollection as $group) {
                if (strtolower($group->getAttributeGroupName()) == 'prices') {
                    $after = 'group_'.$group->getId();
                    break;
                }
            }
        }
        if (!$after) {
            $tabIds     = $block->getTabsIds();
            $after      = ((array_search('inventory', $tabIds) !== false) && (array_search('inventory', $tabIds) > 0)) ? 
                $tabIds[array_search('inventory', $tabIds) - 1] : 'websites';
        }
        $block->addTab('zone_price', array(
            'after'     => $after, 
            'label'     => $helper->__('Zone Discounts'), 
            'content'   => $tabBlock->toHtml(), 
        ));
        return $this;
    }
}