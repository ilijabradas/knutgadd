<?php

class PI_Oopsprofile_Model_Customergroup_Convert_Adapter_Customergroup
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
    public function load()
    {
    		try
    		{
						$collection = $this->getCustomerGroupModel()
								->getCollection();
						$entityIds = $collection->getAllIds();    
						$message = Mage::helper('eav')->__("Loaded %d records", count($entityIds));
						$this->addException($message);
        }
        catch (Varien_Convert_Exception $e) {
            throw $e;
        }
        catch (Exception $e) {
            $message = Mage::helper('eav')->__('Problem loading the collection, aborting. Error: %s', $e->getMessage());
            $this->addException($message, Varien_Convert_Exception::FATAL);
        }       
				/**
         * Set collection ids
         */
        $this->setData($entityIds);
        return $this;
    }

    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    /*
     * saveRow function for saving each customer group
     *
     * params args array
     * return array
     */
    public function saveRow($importData)
    {
    		try
    		{
    				$taxClassModel = Mage::getModel('tax/class');
    				$taxClassIds = $taxClassModel->getCollection()->getAllIds();
				    $customergroup = $this->getCustomerGroupModel();

				    if(!empty($importData))
				    {
				    		if(array_search($importData['tax_class_id'],$taxClassIds))
				    		{
										$customergroup->setTaxClassId($importData['tax_class_id'])->setCustomerGroupCode($importData['customer_group_code']);
										$groupId = $customergroup->getCollection()->addFieldToFilter('customer_group_code',$importData['customer_group_code'])->getFirstItem()->getId();
										if(!empty($groupId))
										{
												$customergroup->setId($groupId);
										}        		
										else
										{
												$customergroup->setId(null);
										}

										$customergroup->save();
								}
								else
								{
										$message = Mage::helper('oopsprofile')->__('tax_class_id %s is not exist',$importData['tax_class_id']);
				        		Mage::throwException($message);
								}
				    }
				    else
				    {
				    		$message = Mage::helper('oopsprofile')->__('There is no row to import');
				        Mage::throwException($message);
				    }

				    
				}
				catch (Varien_Convert_Exception $e) 
				{
	          throw $e;
	      }
	      
        return $this;
    }

    public function getCustomerGroupModel()
    {
    		return Mage::getModel('customer/group');
    }
}
