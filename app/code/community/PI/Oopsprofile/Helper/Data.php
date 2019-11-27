<?php

class PI_Oopsprofile_Helper_Data extends Mage_Core_Helper_Abstract
{
		/**Run Profile On the basis of profile id**/
		public function RunOopsProfile($profileId)
		{
				
        if ($profileId) {
        		$profile = Mage::getModel('dataflow/profile')->load($profileId);
        		//$profile->load($profileId);
        		$id = $profile->getId();
            if (empty($id))//if profile not exist
            {
                return;
            }
            else
            {
        				$oopsprofile = Mage::getModel('oopsprofile/oopsprofile')
															->getCollection()
															->addFieldToFilter('profile_enable','1')
															->addFieldToFilter('dataflow_profile_id',$id)
															->getFirstItem();
								$enable = $oopsprofile->getProfileEnable();
								if($enable == 1)//if profile is disable
								{
										return;
								}            		
            		else
            		{
				        		Mage::register('current_convert_profile', $profile);
				        		$profile->run();
				        		Mage::unregister('current_convert_profile');
				        }
            }
        }
        
        return;
		}

		public function entityTypeArray()
    {
			$arr['product'] = 'Products';
			$arr['customer'] = 'Customers';
			return $arr;
		}

		public function entityAttrTypeArray()
    {
			$arr = array(	'product', 
										'customer',
									);
			return $arr;
		}

		public function wayTypeArray()
    {
			$arr['file'] = 'Local Server';
			$arr['http'] = 'Http Server';	
			return $arr;
		}
		public function formatTypeArray()
    {
			
				$arr['csv'] = 'CSV';
				$arr['txt'] = 'TXT';
	
				return $arr;
		}
		public function delimiterTypeArray()
    {
			
				$arr[','] = 'Comma';
				$arr['\t'] = 'Tab';
				$arr[':'] = 'Colon';
				$arr[' '] = 'Space';
				$arr['|'] = 'Vertical Pipe';
				$arr[';'] = 'Semicolon';
	
				return $arr;
		}
		public function enclosureTypeArray()
    {
			
				$arr['"'] = '"';
				$arr["'"] = "'";
	
				return $arr;
		}

		/**Fetch Product's attributes**/
		public function getProductAttributeData()
		{
				$productAttrs = Mage::getResourceModel('catalog/product_attribute_collection')->getData();
				foreach($productAttrs as $productAttr)
				{
						if($productAttr['frontend_label']!='')
						{
							$attr[$productAttr['attribute_code']] = $productAttr['frontend_label'];
						}
				}
				$attr['url_path'] = 'Url Path';
				return $attr;
		}

		/**Fetch Product's stock fields**/
		public function getStockFieldsArray()
		{
				$colData='';
				$resource = Mage::getSingleton('core/resource');
				$stockTable = $resource->getTableName('cataloginventory_stock_item');
				$read = $resource->getConnection('core_read');
				$query = 'show columns from '.$stockTable;
				$fields = $read->fetchAll($query);
				$unsetKeys = array(
											'item_id',
											'product_id',
											'stock_id',
										);
				foreach($fields as $field)
				{
						$colName = $field['Field'];
						$value = ucwords(implode(' ',explode('_',$colName)));
						//$colData['inventory_'.$colName]=$value;
						$colData[$colName]=$value;
						if(in_array($colName,$unsetKeys))
						{
								unset($colData[$colName]);
						}
				}
				return $colData;
		}

		


		/**Fetch Dataflow profile id and name**/
		public function getProfileArray()
		{

				$profileArray = array();
				$OopsDataProfileIds = array();

				$oopsProfiles = Mage::getModel('oopsprofile/oopsprofile')
													->getCollection()
													->setOrder('oops_profile_id');

				$profileArray[0] = '--Select a profile--';
				foreach($oopsProfiles as $oopsprofile)
				{
						$profile = '';
						$OopsDataProfileIds = $oopsprofile->getDataflowProfileId();
						$profile = Mage::getModel('dataflow/profile')->getCollection()
											->addFieldToFilter('profile_id',$OopsDataProfileIds)
											->addFieldToFilter('entity_type','product')
											->getFirstItem();
						if($profile->getId())
						{
								$profileArray[$profile->getId()] = $profile->getName();
						}
						else
						{
								continue;
						}
				}

				return $profileArray;
		} 

		/**Fetch Category Mapping id and name**/
		public function getMappingArray()
		{
				$mappings = Mage::getModel('oopsprofile/mapping')->getCollection();

				$mappingArray = array();

				$mappingArray[0] = '--Select a mapping--';
				foreach($mappings as $mapping)
				{
						$mappingArray[$mapping->getId()] = $mapping->getCategoryMapName();
				}
				return $mappingArray;
		} 

}
