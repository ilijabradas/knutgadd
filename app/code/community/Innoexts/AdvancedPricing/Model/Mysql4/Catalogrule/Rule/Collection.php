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
 * Catalog rule collection resource
 * 
 * @category   Innoexts
 * @package    Innoexts_AdvancedPricing
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_AdvancedPricing_Model_Mysql4_Catalogrule_Rule_Collection 
    extends Mage_CatalogRule_Model_Mysql4_Rule_Collection 
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'catalogrule_rule_collection';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'collection';
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
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    public function getVersionHelper()
    {
        return $this->getAdvancedPricingHelper()->getVersionHelper();
    }
    /**
     * Constructor
     */
    protected function _construct()
    {
        if ($this->getVersionHelper()->isGe1700()) {
            $this->_associatedEntitiesMap['store'] = array(
                'associations_table'    => 'catalogrule/store', 
                'rule_id_field'         => 'rule_id', 
                'entity_id_field'       => 'store_id', 
            );
            $this->_associatedEntitiesMap['currency'] = array(
                'associations_table'    => 'catalogrule/currency', 
                'rule_id_field'         => 'rule_id', 
                'entity_id_field'       => 'currency', 
            );
        }
        parent::_construct();
    }
    /**
     * Limit rules collection by specific stores
     * 
     * @param int|array|Mage_Core_Model_Store $storeId
     * 
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addStoreFilter($storeId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('store');
        if (!$this->getFlag('is_store_table_joined')) {
            $this->setFlag('is_store_table_joined', true);
            if ($storeId instanceof Mage_Core_Model_Store) {
                $storeId = $storeId->getId();
            }
            $subSelect = $this->getConnection()->select()
                ->from(array('store' => $this->getTable($entityInfo['associations_table'])), '')
                ->where('store.' . $entityInfo['entity_id_field'] . ' IN (?)', $storeId);
            $this->getSelect()->exists(
                $subSelect, 'main_table.'.$entityInfo['rule_id_field'].' = store.'.$entityInfo['rule_id_field']
            );
        }
        return $this;
    }
    /**
     * Provide support for store id filter
     *
     * @param string $field
     * @param mixed $condition
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_ids') {
            return $this->addStoreFilter($condition);
        }
        parent::addFieldToFilter($field, $condition);
        return $this;
    }
}