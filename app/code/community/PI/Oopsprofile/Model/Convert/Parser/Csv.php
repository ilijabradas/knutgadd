<?php 

class PI_Oopsprofile_Model_Convert_Parser_Csv extends Mage_Dataflow_Model_Convert_Parser_Csv
{

		/**
   * Read data collection and write to temporary file
   *
   * @return PI_Oopsprofile_Model_Convert_Parser_Csv
   */
    public function unparse()
    {
        $batchExport = $this->getBatchExportModel()
            ->setBatchId($this->getBatchModel()->getId());
        $fieldList = $this->getBatchModel()->getFieldList();
        $batchExportIds = $batchExport->getIdCollection();

        $io = $this->getBatchModel()->getIoAdapter();
        


				/**
				* Save products separately
				**/
        $profileId = Mage::registry('current_convert_profile')->getId();

        $entityType = Mage::registry('current_convert_profile')->getEntityType();

				$oopprofile = Mage::getModel('oopsprofile/oopsprofile')->getCollection()
																															->addFieldToFilter('dataflow_profile_id',$profileId)
																															->getFirstItem();

				$saveMultipleCopies = $oopprofile->getSaveProductsSeparately();
				if($saveMultipleCopies==1 && $entityType=='product')//if "Save Products Separately" enable
				{
						
						if (!$batchExportIds) {
						    $io->write("");
						    $io->close();
						    return $this;
						}

						$i=1;
						foreach ($batchExportIds as $batchExportId) {

								$io->openMulty($i++);
								

								if ($this->getVar('fieldnames')) {
								    $csvData = $this->getCsvString($fieldList);
								    $io->write($csvData);
								}
						
						    $csvData = array();
						    $batchExport->load($batchExportId);
						    $row = $batchExport->getBatchData();

						    foreach ($fieldList as $field) {
						        $csvData[] = isset($row[$field]) ? $row[$field] : '';
						    }
						    $csvData = $this->getCsvString($csvData);
						    $io->write($csvData);
						    $io->close();
						}
				}
				else
				{
						$io->open();			
        

				    if (!$batchExportIds) {
				        $io->write("");
				        $io->close();
				        return $this;
				    }

				    if ($this->getVar('fieldnames')) {
				        $csvData = $this->getCsvString($fieldList);
				        $io->write($csvData);
				    }

				    foreach ($batchExportIds as $batchExportId) {
				        $csvData = array();
				        $batchExport->load($batchExportId);
				        $row = $batchExport->getBatchData();

				        foreach ($fieldList as $field) {
				            $csvData[] = isset($row[$field]) ? $row[$field] : '';
				        }
				        $csvData = $this->getCsvString($csvData);
				        $io->write($csvData);
				    }

				    $io->close();
				}

        return $this;
    }


    public function parse()
    {
        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        if ($fDel == '\t') {
            $fDel = "\t";
        }

        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        }
        catch (Exception $e) {
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!method_exists($adapter, $adapterMethod)) {
            $message = Mage::helper('dataflow')->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();
        $batchIoAdapter = $this->getBatchModel()->getIoAdapter();

        if (Mage::app()->getRequest()->getParam('files')) {
            $file = Mage::app()->getConfig()->getTempVarDir().'/import/'
                . urldecode(Mage::app()->getRequest()->getParam('files'));
            $this->_copy($file);
        }

        $batchIoAdapter->open(false);

       	$isFieldNames = $this->getVar('fieldnames', '') == 'true' ? true : false;
        if (!$isFieldNames && is_array($this->getVar('map'))) {
            $fieldNames = $this->getVar('map');
        }
        else {
        
            $fieldNames = array();
            foreach ($batchIoAdapter->read(true, $fDel, $fEnc) as $v) {
                $fieldNames[$v] = $v;
            }

            if(is_array($this->getVar('map')))
            {
            		$mappedfields = $this->getVar('map');

            		$fieldNames = array_merge($fieldNames,$mappedfields);
            }


        }

        //echo '<pre>';print_r($fieldNames);exit;

        $countRows = 0;
        while (($csvData = $batchIoAdapter->read(true, $fDel, $fEnc)) !== false) {
            if (count($csvData) == 1 && $csvData[0] === null) {
                continue;
            }


            $itemData = array();
            $countRows ++; $i = 0;
            foreach ($fieldNames as $field) {
                $itemData[$field] = isset($csvData[$i]) ? $csvData[$i] : null;
                $i ++;
            }
						//echo '<pre>';print_r($itemData);
            $batchImportModel = $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($itemData)
                ->setStatus(1)
                ->save();
        }


        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $countRows));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        //$adapter->$adapterMethod();

        return $this;
    }

}
?>
