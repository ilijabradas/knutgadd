<?php

class PI_Oopsprofile_Model_Dataflow_Convert_Mapper_Column extends Mage_Dataflow_Model_Convert_Mapper_Column
{   
    public function map()
    {
    
        $batchModel  = $this->getBatchModel();
        $batchExport = $this->getBatchExportModel();

        $batchExportIds = $batchExport
            ->setBatchId($this->getBatchModel()->getId())
            ->getIdCollection();
            

        if ($this->getVar('map') && is_array($this->getVar('map'))) {
            $attributesToSelect = $this->getVar('map');
        }
        else {
            $attributesToSelect = array();
        }


				/**Fetch category mapping detail**/
				$profileId = Mage::registry('current_convert_profile')->getId();

				$oopsrofile = Mage::getModel('oopsprofile/oopsprofile')->getCollection()
																															->addFieldToFilter('dataflow_profile_id',$profileId)
																															->getFirstItem();
				$selectedProfile = $oopsrofile->getProfileTemplate();

				$tempModel = Mage::getModel('oopsprofile/template')->load($selectedProfile);

				$categoryMapping = $tempModel->getCategoryMapping();

				$mappingDetail = '';

				if($categoryMapping!='')
    		{
    				$categoryMappingModel = Mage::getModel('oopsprofile/mapping')->load($categoryMapping);
    				$mappingDetail = unserialize($categoryMappingModel->getMappingDetail()); 
    		}
				
				//print_r($categoryMapping);
        

        foreach ($batchExportIds as $batchExportId) {
            $batchExport = $this->getBatchExportModel()->load($batchExportId);
            $row = $batchExport->getBatchData();

						$newRow = array();
						
						foreach($row as $field => $value)
						{

								if($field == 'category_ids' && $value!='')
								{								
										$value = $this->_fetchCategoryNames($value,$mappingDetail);
								}
								
								if(!empty($attributesToSelect[$field])) //apply field mapping
								{
										$newRow[$attributesToSelect[$field]] = $value;
								}
								else
								{
										$newRow[$field] = $value;
								}
						}

            $batchExport->setBatchData($newRow)
                ->setStatus(2)
                ->save();
            $this->getBatchModel()->parseFieldList($batchExport->getBatchData());
        }

        //exit;

        return $this;
    }

    protected function _fetchCategoryNames($category_ids,$mappingDetail)
    {
    		$category_ids = explode(',',$category_ids);
    		$categoryCollection = Mage::getModel('catalog/category')->getCollection()
    																														->addAttributeToSelect('name')
    																														->addAttributeToFilter('entity_id',array('in'=>array($category_ids)));
    		

    		$categoryName = array();
    		foreach($categoryCollection as $category)
    		{
    				
    				if(!empty($mappingDetail))
    				{
    						if(!empty($mappingDetail[$category->getId()]))
    						{
    								$categoryName[] = $mappingDetail[$category->getId()];
    						}
    						else
    						{
    								$categoryName[] = $category->getName();
    						}
    				}
						else
						{
								$categoryName[] = $category->getName();
						}
    		}
    		$categoryName = implode(',',$categoryName);
    		//echo '<pre>';print_r($categoryName);
    		return $categoryName;
    }
}
