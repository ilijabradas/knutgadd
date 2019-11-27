<?php

class PI_Oopsprofile_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract
{
    /**
     * Add special attributes
     *
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['attribute_set_id'] = Mage::helper('catalogrule')->__('Attribute Set');
        $attributes['category_ids'] = Mage::helper('catalogrule')->__('Category');
        //$attributes['sku'] = Mage::helper('catalogrule')->__('Sku');
        //$attributes['entity_id'] = Mage::helper('catalogrule')->__('Id');
    }
}
