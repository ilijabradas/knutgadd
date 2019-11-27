<?php
class PI_Oopsprofile_Model_Convert_Adapter_Io extends Mage_Dataflow_Model_Convert_Adapter_Io
{


		/**
     * @return Varien_Io_Abstract or PI_Oopsprofile_Model_Varien_Io_sftp in case of sftp
     */
    public function getResource($forWrite = false)
    {
        if (!$this->_resource) {
            $type = $this->getVar('type', 'file');
            $className = 'Varien_Io_' . ucwords($type);
            if($this->getVar('type')=='sftp')
            {
            		$className = 'PI_Oopsprofile_Model_Varien_Io_sftp';
            }
            $this->_resource = new $className();

            $isError = false;

            $ioConfig = $this->getVars();
            switch ($this->getVar('type', 'file')) {
                case 'file':
                    //validate export/import path
                    $path = rtrim($ioConfig['path'], '\\/')
                          . DS . $ioConfig['filename'];
                    /** @var $validator Mage_Core_Model_File_Validator_AvailablePath */
                    $validator = Mage::getModel('core/file_validator_availablePath');
                    $validator->setPaths( Mage::getStoreConfig(self::XML_PATH_EXPORT_LOCAL_VALID_PATH) );
                    if (!$validator->isValid($path)) {
                        foreach ($validator->getMessages() as $message) {
                            Mage::throwException($message);
                            return false;
                        }
                    }

                    if (preg_match('#^' . preg_quote(DS, '#').'#', $this->getVar('path')) ||
                        preg_match('#^[a-z]:' . preg_quote(DS, '#') . '#i', $this->getVar('path'))) {

                        $path = $this->_resource->getCleanPath($this->getVar('path'));
                    } else {
                        $baseDir = Mage::getBaseDir();
                        $path = $this->_resource->getCleanPath($baseDir . DS . trim($this->getVar('path'), DS));
                    }

                    $this->_resource->checkAndCreateFolder($path);

                    $realPath = realpath($path);

                    if (!$isError && $realPath === false) {
                        $message = Mage::helper('dataflow')->__('The destination folder "%s" does not exist or there is no access to create it.', $ioConfig['path']);
                        Mage::throwException($message);
                    } elseif (!$isError && !is_dir($realPath)) {
                        $message = Mage::helper('dataflow')->__('Destination folder "%s" is not a directory.', $realPath);
                        Mage::throwException($message);
                    } elseif (!$isError) {
                        if ($forWrite && !is_writeable($realPath)) {
                            $message = Mage::helper('dataflow')->__('Destination folder "%s" is not writable.', $realPath);
                            Mage::throwException($message);
                        } else {
                            $ioConfig['path'] = rtrim($realPath, DS);
                        }
                    }
                    break;
                default:
                    $ioConfig['path'] = rtrim($this->getVar('path'), '/');
                    break;
            }

            if ($isError) {
                return false;
            }
            try {
                $this->_resource->open($ioConfig);
            } catch (Exception $e) {
                $message = Mage::helper('dataflow')->__('An error occurred while opening file: "%s".', $e->getMessage());
                Mage::throwException($message);
            }
        }
        return $this->_resource;
    }

		/**
     * Load data
     *
     * @return PI_Oopsprofile_Model_Convert_Adapter_Io
     */
    public function load()
    {
        if (!$this->getResource()) {
            return $this;
        }

        $result = '';

				$filename = '';
				$fileformat = $this->getVar('fileformat');

				$batchModel = Mage::getSingleton('dataflow/batch');
		    $destFile = $batchModel->getIoAdapter()->getFile(true);
		    $filename = $this->getVar('filename');

		    if($this->getVar('path') && $fileformat=='xml')
				{
						$filename = $this->getVar('path').'/'.$filename;
				}

				if($fileformat=='xml' || $fileformat=='xls' || $fileformat=='xlsx')
        {
        		if($this->getVar('type')=='sftp')
        		{
								$filename = $this->saveOnLocalFromSftp();//copy file from ftp to local server 
						} 
						else if($this->getVar('type')=='ftp')
				    {
								$filename = $this->saveOnLocalFromFtp();//copy file from ftp to local server     
				    } 
        }
        else
        {

					  if($this->getVar('path') && $this->getVar('type')=='sftp')
						{
								$filename = $this->getVar('path').'/'.$filename;
						}
						
				    $result = $this->getResource()->read($filename, $destFile);
        
        		$filename = $this->getResource()->pwd() . '/' . $this->getVar('filename');

        		if (false === $result) {
				        $message = Mage::helper('dataflow')->__('Could not load file: "%s".', $filename);
				        Mage::throwException($message);
				    } else {
				        $message = Mage::helper('dataflow')->__('Loaded successfully: "%s".', $filename);
				        $this->addException($message);
				    }
        }

				Mage::register('filename_with_path',$filename);// set file name with path       

        $this->setData($result);
        return $this;
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
						if($this->getVar('type')=='ftp')
						{
								$this->saveOnFtp();//copy local Excel file from temp folder(var/export/ftp) to remote server
						}
						if($this->getVar('type')=='sftp')
						{
								$file = 'var/export/ftp/export_file.'.$this->getVar('fileformat');//temp file path on local server
								$remote_file = $this->getVar('filename');
								if($this->getVar('path'))
								{
										$remote_file = $this->getVar('path').'/'.$remote_file;
								}
								$result   = $this->getResource()->write($remote_file, $file);
						}
        
        		return $this;
        }


        $batchModel = Mage::getSingleton('dataflow/batch');

        $dataFile = $batchModel->getIoAdapter()->getFile(true);   		
        

        $filename = $this->getVar('filename');

        if($this->getVar('path') && $this->getVar('type')=='sftp')
				{
						$filename = $this->getVar('path').'/'.$filename;
				}



				/**
				* Save products separately in case of all formats except xls and xlsx
				**/
				$profileId = Mage::registry('current_convert_profile')->getId();

				$entityType = Mage::registry('current_convert_profile')->getEntityType();

				$oopprofile = Mage::getModel('oopsprofile/oopsprofile')->getCollection()
																															->addFieldToFilter('dataflow_profile_id',$profileId)
																															->getFirstItem();

				$saveMultipleCopies = $oopprofile->getSaveProductsSeparately();
				if($saveMultipleCopies==1  && $entityType=='product')//if "Save Products Separately" enable
				{
						$batchId = $batchModel->getId();

						//$i = 1;
						$fileName = $filename;
						foreach(glob('var/tmp/batch_'.$batchId.'*.tmp') as $dataFileName)
						{
								$dFile1 = explode('.',$dataFileName);
								$dFile2 = explode('_',$dFile1[0]);
								$id = $dFile2[count($dFile2)-1];
						
								$filename = '';
								$explodedata = explode('.',$fileName);
								$Name = $explodedata[0].'_'.$id;
								$filename = $Name.'.'.$explodedata[1];
								$result   = $this->getResource()->write($filename, $dataFileName, 0777);
								if (false === $result) {
									  $message = Mage::helper('dataflow')->__('Could not save file: %s.', $filename);
									  Mage::throwException($message);
								} else {

										/**
										* Save Local copies in case of all formats except xls and xlsx
										**/
										$profileId = Mage::registry('current_convert_profile')->getId();

										$oopprofile = Mage::getModel('oopsprofile/oopsprofile')->getCollection()
																																					->addFieldToFilter('dataflow_profile_id',$profileId)
																																					->getFirstItem();

										$saveLocalCopies = $oopprofile->getSaveLocalCopy();

										if($saveLocalCopies)//save if "save local copies" enable
										{
												$localpath = 'oopsprofile/'.$oopprofile->getId();
												//create path if not exist
												if (!file_exists($localpath)) {
														mkdir($localpath, 0777, true);
												}
												$fileWithPath = $localpath.'/'.$filename;

												$localResource = new Varien_Io_File();

												$localResource->write($fileWithPath, $dataFileName, 0777);
										}

					
							
									  $message = Mage::helper('dataflow')->__('Saved successfully: "%s" [%d byte(s)].', $filename, $batchModel->getIoAdapter()->getFileSize());
									  if ($this->getVar('link')) {
									      $message .= Mage::helper('dataflow')->__('<a href="%s" target="_blank">Link</a>', $this->getVar('link'));
									  }
									  $this->addException($message);
								}
						}
				}
				else
				{
						
					  $result   = $this->getResource()->write($filename, $dataFile, 0777);

					  if (false === $result) {
					      $message = Mage::helper('dataflow')->__('Could not save file: %s.', $filename);
					      Mage::throwException($message);
					  } else {

								/**
								* Save Local copies in case of all formats except xls and xlsx
								**/
								$profileId = Mage::registry('current_convert_profile')->getId();

								$oopprofile = Mage::getModel('oopsprofile/oopsprofile')->getCollection()
																																			->addFieldToFilter('dataflow_profile_id',$profileId)
																																			->getFirstItem();

								$saveLocalCopies = $oopprofile->getSaveLocalCopy();

								if($saveLocalCopies)//save if "save local copies" enable
								{
										$localpath = 'oopsprofile/'.$oopprofile->getId();
										//create path if not exist
										if (!file_exists($localpath)) {
												mkdir($localpath, 0777, true);
										}
										$fileWithPath = $localpath.'/'.$this->getVar('filename');

										$localResource = new Varien_Io_File();

										$localResource->write($fileWithPath, $dataFile, 0777);
								}

					
					  
					      $message = Mage::helper('dataflow')->__('Saved successfully: "%s" [%d byte(s)].', $filename, $batchModel->getIoAdapter()->getFileSize());
					      if ($this->getVar('link')) {
					          $message .= Mage::helper('dataflow')->__('<a href="%s" target="_blank">Link</a>', $this->getVar('link'));
					      }
					      $this->addException($message);
					  }
				}

				
        return $this;
    }

    public function saveOnFtp()
    {
    		$file = 'var/export/ftp/export_file.'.$this->getVar('fileformat');//temp file path on local server
				$remote_file = $this->getVar('filename');

				// set up basic connection
				$conn_id = ftp_connect($this->getVar('host'));

				// login with username and password
				$login_result = ftp_login($conn_id, $this->getVar('user'), $this->getVar('password'));

				if($this->getVar('file_mode')==2)
				{
						$fileMode = FTP_BINARY;
				}
				else
				{
						$fileMode = FTP_ASCII;
				}


				if($this->getVar('path'))
				{
						$remote_file = $this->getVar('path').'/'.$remote_file;
						//ftp_chdir($conn_id, $this->getVar('path'));
				}

				// upload a file
				if (ftp_put($conn_id, $remote_file, $file, $fileMode)) {
				$message = Mage::helper('dataflow')->__('successfully uploaded '). $remote_file;
				$this->addException($message);
				} else {
				 $message = Mage::helper('dataflow')->__('There was a problem while uploading '). $remote_file;
				 Mage::throwException($message);
				}

				// close the connection and the file handler
				ftp_close($conn_id);

				return $this;				
    }

    public function saveOnLocalFromFtp()
    {
    		$path = 'var/import/ftp';
    		$file = $path.'/import_file.'.$this->getVar('fileformat');//temp file path on local server
				$remote_file = $this->getVar('filename');

				//create path if not exist
				if (!file_exists($path)) {
						mkdir($path, 0777, true);
				}

				// set up basic connection
				$conn_id = ftp_connect($this->getVar('host'));

				// login with username and password
				$login_result = ftp_login($conn_id, $this->getVar('user'), $this->getVar('password'));

				if($this->getVar('file_mode')==2)
				{
						$fileMode = FTP_BINARY;
				}
				else
				{
						$fileMode = FTP_ASCII;
				}


				if($this->getVar('path'))
				{
						$remote_file = $this->getVar('path').'/'.$remote_file;
						//ftp_chdir($conn_id, $this->getVar('path'));
				}

				// upload a file
				if (ftp_get($conn_id, $file,$remote_file, $fileMode)) {
				$message = Mage::helper('dataflow')->__('successfully loaded '). $remote_file;
				$this->addException($message);
				} else {
				 $message = Mage::helper('dataflow')->__('There was a problem while loading '). $remote_file;
				 Mage::throwException($message);
				}
				// close the connection and the file handler
				ftp_close($conn_id);

				return $file;
    }

    public function saveOnLocalFromSftp()
    {
    		$path = 'var/import/ftp';
    		$file = $path.'/import_file.'.$this->getVar('fileformat');//temp file path on local server
				$remote_file = $this->getVar('filename');

				//create path if not exist
				if (!file_exists($path)) {
						mkdir($path, 0777, true);
				}
				if($this->getVar('path'))
				{
						$remote_file = $this->getVar('path').'/'.$remote_file;
				}
				$result = $this->getResource()->read($remote_file,$file);

				// upload a file
				if ($result===false) {
				$message = Mage::helper('dataflow')->__('There was a problem while loading '). $remote_file;
				Mage::throwException($message);				
				} else {
				 $message = Mage::helper('dataflow')->__('successfully loaded '). $remote_file;
				 $this->addException($message);
				}
				return $file;
				
    }
}
