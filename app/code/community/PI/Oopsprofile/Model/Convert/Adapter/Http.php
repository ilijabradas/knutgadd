<?php

class PI_Oopsprofile_Model_Convert_Adapter_Http extends Mage_Dataflow_Model_Convert_Adapter_Http
{

		/**
     * @return Varien_Io_File
     */
    public function getResource($forWrite = false)
    {
        if (!$this->_resource) {
            $className = 'Varien_Io_File';
        
            $this->_resource = new $className();
                    
         }
        return $this->_resource;
    }


		/**
     * Save result to destination file from temporary
     *
     * @return PI_Oopsprofile_Model_Convert_Adapter_Io
     */
    public function save()
    {
        if (!$this->getResource(true)) {
            return $this;
        }

        $fileformat = $this->getVar('fileformat');


				/***
				**** Code if format is xls or xlsx
				***/
        if($fileformat=='xls' || $fileformat=='xlsx')
        {
        		return $this;
        }
        

        $batchModel = Mage::getSingleton('dataflow/batch');

        $dataFile = $batchModel->getIoAdapter()->getFile(true);

        $path = 'httpexport';

        //create path if not exist
				if (!file_exists($path)) {
						mkdir($path, 0777, true);
				}

				$profile = Mage::registry('current_convert_profile');//current profile 

        $filename = $path.'/export_'.$this->getVar('entitytype').'_'.$profile->getId().'.'.$fileformat;

        $result   = $this->getResource()->write($filename, $dataFile, 0777);

        if (false === $result) {
            $message = Mage::helper('dataflow')->__('Could not save file: %s.', $filename);
            Mage::throwException($message);
        } else {
            $message = Mage::helper('dataflow')->__('Saved successfully: "%s" [%d byte(s)].', $filename, $batchModel->getIoAdapter()->getFileSize());
            if ($this->getVar('link')) {
                $message .= Mage::helper('dataflow')->__('<a href="%s" target="_blank">Link</a>', $this->getVar('link'));
            }
            $this->addException($message);
        }
        return $this;
    }
}
