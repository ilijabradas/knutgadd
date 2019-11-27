<?php

class PI_Oopsprofile_Model_Rule extends Mage_Rule_Model_Abstract
{
     /**
     * Getter for rule combine conditions instance
     *
     * @return PI_Oopsprofile_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('oopsprofile/rule_condition_combine');
    }

    public function getConditions()
    {
    		$profile = Mage::registry('current_convert_profile');

				$ruleCollection = Mage::getModel('oopsprofile/oopsprofile')
        										->getCollection()
        										->addFieldToFilter('dataflow_profile_id ',$profile->getId())
        										->getFirstItem();
    
        if (empty($this->_conditions)) {
            $this->_resetConditions();
        }

        // Load rule conditions if it is applicable
        if ($ruleCollection->hasConditionsSerialized()) {
            $conditions = $ruleCollection->getConditionsSerialized();
            if (!empty($conditions)) {
                $conditions = unserialize($conditions);
                if (is_array($conditions) && !empty($conditions)) {
                    $this->_conditions->loadArray($conditions);
                }
            }
            $this->unsConditionsSerialized();
        }

        return $this->_conditions;
    }

    public function loadPost(array $data)
    {
        $arr = $this->_convertFlatToRecursive($data);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }

        return $arr;
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Mage_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return '';
    }
}
