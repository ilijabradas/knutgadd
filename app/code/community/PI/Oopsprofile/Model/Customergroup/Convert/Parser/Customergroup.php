<?php

class PI_Oopsprofile_Model_Customergroup_Convert_Parser_Customergroup
    extends Mage_Eav_Model_Convert_Parser_Abstract
{

	/**
   * Function must be provided but has no meaning here.
   */

   public function parse()
   {
   		
   }

   public function unparse()
   {
   			try
   			{
				 		$entityIds = $this->getData();
					 	foreach ($entityIds as $entityId) 
					 	{
								$customergroup = Mage::getModel('customer/group')
																				->load($entityId)->getData();

								//echo '<pre>';print_r($customergroup);exit;
								unset($customergroup['customer_group_id']);

								$batchExport = $this->getBatchExportModel()
					        ->setId(null)
					        ->setBatchId($this->getBatchModel()->getId())
					        ->setBatchData($customergroup)
					        ->setStatus(1)
					        ->save();

					      //parse field list data to batch model
              	$this->getBatchModel()->parseFieldList($customergroup);
					 	}
				}
				catch (Varien_Convert_Exception $e) {
	          throw $e;
	      }

		   	return $this;
		   	
   }
}
