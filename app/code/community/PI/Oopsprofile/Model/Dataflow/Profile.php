<?php

class PI_Oopsprofile_Model_Dataflow_Profile extends Mage_Dataflow_Model_Profile {

    protected function _beforeSave() {
    		$data = $this->getData();
    		if(!empty($data['oops_profile']))
    		{    	
				    if ($data['oops_profile']) {//saving data for oops profile
				        $this->_oopsbeforeSave();
				    } 
				}
				else 
				{
		        parent::_beforeSave();
		    }
    }

		/**
		**** Upload File On Basis of Profile Way ****
		**/
    protected function uploadImportFile($_files,$selectedFormat,$guiDataFile)
    {
    		$profileWay = $guiDataFile['type'];
    		$fileName = $guiDataFile['filename'];
    		$filePath = $guiDataFile['path'];
				

				$uplodedInfo = array();
    		
				/* Get the uploaded file information */
				$name_of_uploaded_file = basename($_files['uploaded_file']['name']);

				$type_of_uploaded_file = substr($name_of_uploaded_file, strrpos($name_of_uploaded_file, '.') + 1);

				$size_of_uploaded_file = $_files["uploaded_file"]["size"] / 1024; //size in KBs

				//Settings
				$max_allowed_file_size = 1000; // size in KB
				$allowed_extensions = array($selectedFormat);

				if ($size_of_uploaded_file > $max_allowed_file_size) 
				{
            $errors = Mage::helper('oopsprofile')->__("Size of uploded file should be less than $max_allowed_file_size KB");
            Mage::throwException($errors);
        }
        if(!in_array($type_of_uploaded_file, $allowed_extensions))
        {
        		$errors = Mage::helper('oopsprofile')->__("The uploaded file type $type_of_uploaded_file is not allowed for current profile");
            Mage::throwException($errors);
        }

        if($fileName=='')//if filename is not defined
        {
        		$fileName = $name_of_uploaded_file;
        }

        //create path if not exist
				if (!file_exists($filePath) && $filePath!='') {
						mkdir($filePath, 0777, true);
				}

				$tmp_path = $_files["uploaded_file"]["tmp_name"];

				$fileWithPath = $filePath.'/'.$fileName;
				
        if (is_uploaded_file($tmp_path)) 
        {
		      	switch($profileWay)
		      	{
		      			case 'file':
								    $localResource = new Varien_Io_File();
										$localResource->write($fileWithPath, $tmp_path, 0777);
										break;
								case 'ftp':
								case 'sftp':
										$this->saveOnFtp($tmp_path,$fileName,$guiDataFile);
										break;
		        }	
        }

        $uplodedInfo['filename'] = $fileName;
        
    		return $uplodedInfo;
    }


		/**
			Upload File on Ftp
		**/
    public function saveOnFtp($tmp_path,$fileName,$guiDataFile)
    {
    		$file = $tmp_path;//temp file path on local server
				$remote_file = $fileName;

				// set up basic connection
				$conn_id = ftp_connect($guiDataFile['host']);

				// login with username and password
				$login_result = ftp_login($conn_id, $guiDataFile['user'], $guiDataFile['password']);

				if($guiDataFile['file_mode']==2)
				{
						$fileMode = FTP_BINARY;
				}
				else
				{
						$fileMode = FTP_ASCII;
				}


				if($guiDataFile['path'])
				{
						$remote_file = $guiDataFile['path'].'/'.$remote_file;
						$this->make_directory($conn_id, $guiDataFile['path']);
				}
				else
				{
						$remote_file = $remote_file;
				}

				// upload a file
				if (!ftp_put($conn_id, $remote_file, $file, $fileMode)) 
				{				
					 $message = Mage::helper('dataflow')->__('There was a problem while uploading '). $fileName;
					 Mage::throwException($message);
				}

				// close the connection and the file handler
				ftp_close($conn_id);

				return $this;				
    }

		/**
		Recursively create directory on ftp
		**/
    public function make_directory($ftp_stream, $dir)
		{
				//if directory already exists or can be immediately created return true
				if ($this->ftp_is_dir($ftp_stream, $dir) || @ftp_mkdir($ftp_stream, $dir)) 
						return true;
						
				//otherwise recursively try to make the directory
				if (!$this->make_directory($ftp_stream, dirname($dir))) 
						return false;
						
				// final step to create the directory
				return ftp_mkdir($ftp_stream, $dir);
		}
		public function ftp_is_dir($ftp_stream, $dir)
		{
				// get current directory
				$original_directory = ftp_pwd($ftp_stream);
				
				if ( @ftp_chdir( $ftp_stream, $dir ) ) 
				{
					// If it is a directory, then change the directory back to the original directory
					ftp_chdir( $ftp_stream, $original_directory );
					return true;
				} 
				else 
				{
					return false;
				}
		}


    

    
    protected function _oopsbeforeSave() {
        if (!$this->getId()) {
            $this->isObjectNew(true);
        }
        $actionsXML = $this->getData('actions_xml');
        if (strlen($actionsXML) < 0 &&
                @simplexml_load_string('<data>' . $actionsXML . '</data>', null, LIBXML_NOERROR) === false) {
            Mage::throwException(Mage::helper('dataflow')->__("Actions XML is not valid."));
        }

        if (is_array($this->getGuiData())) {
            $data = $this->getData();
            //echo '<pre>';print_r($data);exit;
            $guiData = $this->getGuiData();
        		
						
            /**
						**** Apply Selected file format ****
						**/
						$guiFileName = explode('.',$guiData['file']['filename']);
		        $guiFileFormat='';
		        if(count($guiFileName)>1)//if file name has format 
						{
		        		$guiFileFormat = array_pop($guiFileName);//fetch the format(fetch the last word of string and remove it from array)
		        }
		        $orgFileFormat = $guiData['parse']['type'];//selected file format

		        if($guiFileFormat != $orgFileFormat)//match file format
		        {
		        		$guiData['file']['filename'] = implode('.',$guiFileName).'.'.$orgFileFormat;//add selected file format in file name
		        }
            

 
            
            $charSingleList = array('\\', '/', '.', '!', '@', '#', '$', '%', '&', '*', '~', '^');
            if (isset($teguiData['file']['type']) && $guiData['file']['type'] == 'file') {
                if (empty($guiData['file']['path'])
                        || (strlen($guiData['file']['path']) == 1
                        && in_array($guiData['file']['path'], $charSingleList))) {
                    $guiData['file']['path'] = self::DEFAULT_EXPORT_PATH;
                }
                if (empty($guiData['file']['filename'])) {
                    $guiData['file']['filename'] = self::DEFAULT_EXPORT_FILENAME . $data['entity_type']
                            . '.' . $guiData['parse']['type'];
                }

                //validate export available path
                $path = rtrim($guiData['file']['path'], '\\/')
                        . DS . $guiData['file']['filename'];
                /** @var $validator Mage_Core_Model_File_Validator_AvailablePath */
                $validator = Mage::getModel('core/file_validator_availablePath');
                /** @var $helperImportExport Mage_ImportExport_Helper_Data */
                $helperImportExport = Mage::helper('importexport');
                $validator->setPaths($helperImportExport->getLocalValidPaths());
                //echo $path;exit;
                if (!$validator->isValid($path)) {
                    foreach ($validator->getMessages() as $message) {
                        Mage::throwException($message);
                    }
                }


                /**
								**** If uploading a file and data transfer ways are "local/ftp/sftp" ****
								**/
								if($this->getDirection()=='import' && !empty($_FILES))
								{
										if($_FILES["uploaded_file"]['name']!='')
										{
												$uploadedData = $this->uploadImportFile($_FILES,$guiData['parse']['type'],$guiData['file']);
												$guiData['file']['filename'] = $uploadedData['filename'];
										}
								}

                $this->setGuiData($guiData);
            }

            if(isset($guiData['file']['type']) && ($guiData['file']['type'] == 'ftp' || $guiData['file']['type'] == 'sftp'))
            {

								/**
								**** If uploading a file and data transfer ways are "local/ftp/sftp" ****
								**/
								if($this->getDirection()=='import' && !empty($_FILES))
								{
										if($_FILES["uploaded_file"]['name']!='')
										{
												$uploadedData = $this->uploadImportFile($_FILES,$guiData['parse']['type'],$guiData['file']);
												$guiData['file']['filename'] = $uploadedData['filename'];
										}
								}
            
            		$this->setGuiData($guiData);
            }

            //generate and save the access token
            $this->generateAuthUrlByService($guiData);

            $this->_parseOopsGuiData();

            $this->setGuiData(serialize($this->getGuiData()));
            //echo '<pre>';print_r($guiData);exit;
        }

        if ($this->_getResource()->isProfileExists($this->getName(), $this->getId())) {
            Mage::throwException(Mage::helper('dataflow')->__("Profile with the same name already exists."));
        }
    }

		protected function _saveAuthUrlToTable($service, $url){
				$oopsProfile = Mage::getModel('oopsprofile/oopsprofile')->loadByProfile($this);
				$oopsProfile->setServiceType($service)->setOauthUrl($url)->setIsTokenAvailable(0)->save();
		}

    /*
     * generate the oAuth token by service
     */
		protected function generateAuthUrlByService($guiData) {

        $service = isset($guiData['email']['service_provider']) ? $guiData['email']['service_provider'] : null;

        if (!is_null($service)) {

            $path = Mage::getBaseDir('var') . DS . $service;
            $file = 'oops_profile_mail_client.tmp';

            switch ($service) {
                case 'gmail':
                    
                    $client = Mage::getSingleton('oopsprofile/mail_google_client');
                    
                    $client->setClientId($guiData['email'][$service]['client_id']);
                    $client->setClientSecret($guiData['email'][$service]['client_secret']);
                    $client->setRedirectUri($guiData['email'][$service]['redirect_uri']);
                    $client->setDeveloperKey($guiData['email'][$service]['developer_key']);

                    $client->setScopes("https://mail.google.com/");

                    $authUrl = $client->createAuthUrl();

										$io = new Varien_Io_File();
                    $io->setAllowCreateFolders(true);
                    $io->open(array('path' => $path));
                    $io->streamOpen($file, 'w');
                    $io->streamLock(true);
                    $guiData['profile_id'] = $this->getId();
                    $io->streamWrite(serialize($guiData));
                    $io->close();

                    $this->_saveAuthUrlToTable($service, $authUrl);
            }
        }
    }

    protected function _parseOopsGuiData() {

        $nl = "\r\n";
        $import = $this->getDirection() === 'import';
        $p = $this->getGuiData();

        $data = $this->getData();
       	
        //$profileTemplate = $data['profile_template'];
        //$profileTemplate = $p['profile_template'];
		$profileTemplate='';

        $applyTemplate = false;
        $tempMappingXml = '';
        $tempDataXml = '';

        if($profileTemplate!=0 && $this->getEntityType() == 'product')//if predefind template is selected
        {
						$applyTemplate = true;

        		$tempMappingXml = $p['field_mapping_xml'];

        		$tempDataXml = $p['data_format_xml'];

        }



        //parent::_parseGuiData();
        switch ($this->getDataTransfer()) {
            case 'email':
                $setXml = $this->generateEmailXML($nl, $import, $p);
                break;
            case 'file':
                $setXml = $this->generateFileXML($nl, $import, $p);
                break;
            case 'http':
                $setXml = $this->generateHttpXML($nl, $import, $p);
                break;
            case 'api':
                $setXml = $this->generateApiXML($nl, $import, $p);
                break;
        }


        if ($import) {
            $setXml .= '<var name="format"><![CDATA[' . $p['parse']['type'] . ']]></var>' . $nl;
        }

        $setXml .= '</action>' . $nl . $nl;
        

        if($this->getDataTransfer()=='api')
        {
        		$p['parse']['type'] = 'xml';
        }

        switch ($p['parse']['type']) {
            case 'csv':
                $parseXml = '<action type="oopsprofile/convert_parser_csv" method="'
                        . ($import ? 'parse' : 'unparse') . '">' . $nl;

                if($applyTemplate)//if predefind template is selected
        				{
										$parseXml .= $tempDataXml;
        				}
                else
                {
				            $parseXml .= '    <var name="delimiter"><![CDATA['
				                    . $p['parse']['csv']['delimiter'] . ']]></var>' . $nl;
				            $parseXml .= '    <var name="enclose"><![CDATA['
                        . $p['parse']['csv']['enclose'] . ']]></var>' . $nl;
                }
                break;
            case 'txt':
                $parseXml = '<action type="oopsprofile/convert_parser_txt" method="'
                        . ($import ? 'parse' : 'unparse') . '">' . $nl;
                if($applyTemplate)//if predefind template is selected
        				{
										$parseXml .= $tempDataXml;
        				}
                else
                {
				            $parseXml .= '    <var name="delimiter"><![CDATA['
				                    . $p['parse']['txt']['delimiter'] . ']]></var>' . $nl;
				            $parseXml .= '    <var name="enclose"><![CDATA['
				                    . $p['parse']['txt']['enclose'] . ']]></var>' . $nl;
				        }
                break;
            case 'xml':
                $parseXml = '<action type="oopsprofile/convert_parser_xml" method="'
                        . ($import ? 'parse' : 'unparse') . '">' . $nl;

                $parseXml .= '    <var name="entity_type">'. $this->getEntityType() .'</var>' . $nl;

                $parseXml .= '    <var name="profile_template">'. $profileTemplate .'</var>' . $nl;

                if($import && $this->getDataTransfer()=='api')
								{
										$parseXml .= '    <var name="data_transfer">api</var>' . $nl;
										$parseXml .= '    <var name="apiurl">'. $p['api']['url'] .'</var>' . $nl;
										$parseXml .= '    <var name="apikey">'. $p['api']['key'] .'</var>' . $nl;
										$parseXml .= '    <var name="apiuser">'. $p['api']['user'] .'</var>' . $nl;
										$parseXml .= '    <var name="apimethod">'. $p['api']['method'] .'</var>' . $nl;
										$parseXml .= '    <var name="apiprofileId">'. $p['api']['profileId'] .'</var>' . $nl;
								}
                break;
            case 'xls':
            		 $parseXml = '<action type="oopsprofile/convert_parser_xls" method="'
                        . ($import ? 'parse' : 'unparse') . '">' . $nl;
                        
								if($this->getDataTransfer()=='file')
								{
                		$parseXml .= '    <var name="path">' . $p['file']['path'] . '</var>' . $nl;
        						$parseXml .= '    <var name="filename"><![CDATA[' . $p['file']['filename'] . ']]></var>' . $nl;
                		$parseXml .= '    <var name="filetype">' . $p['file']['type'] . '</var>' . $nl;
                		
                }
                if($this->getDataTransfer()=='http')
								{
										$parseXml .= '    <var name="filetype">http</var>' . $nl;
										$parseXml .= '    <var name="entitytype">'.$this->getEntityType().'</var>' . $nl;
								}
								if($applyTemplate)//if predefind template is selected
        				{
										$parseXml .= $tempDataXml;
        				}
                break;
            case 'xlsx':
                $parseXml = '<action type="oopsprofile/convert_parser_xlsx" method="'
                        . ($import ? 'parse' : 'unparse') . '">' . $nl;
                        
								if($this->getDataTransfer()=='file')
								{
                		$parseXml .= '    <var name="path">' . $p['file']['path'] . '</var>' . $nl;
        						$parseXml .= '    <var name="filename"><![CDATA[' . $p['file']['filename'] . ']]></var>' . $nl;
                		$parseXml .= '    <var name="filetype">' . $p['file']['type'] . '</var>' . $nl;
                }
                if($this->getDataTransfer()=='http')
								{
										$parseXml .= '    <var name="filetype">http</var>' . $nl;
										$parseXml .= '    <var name="entitytype">'.$this->getEntityType().'</var>' . $nl;
								}
								if($applyTemplate)//if predefind template is selected
        				{
										$parseXml .= $tempDataXml;
        				}
                break;
        }
        if(!$applyTemplate)//if predefind template is not selected
				{
        		$parseXml .= '    <var name="fieldnames">' . $p['parse']['fieldnames'] . '</var>' . $nl;
        }
        $parseXmlInter = $parseXml;
        $parseXml .= '</action>' . $nl . $nl;


				$mapXml = '';

				if (isset($p['map']) && is_array($p['map'])) {
		        foreach ($p['map'] as $side => $fields) {
		            if (!is_array($fields)) {
		                continue;
		            }
		            foreach ($fields['db'] as $i => $k) {
		                if ($k == '' || $k == '0') {
		                    unset($p['map'][$side]['db'][$i]); //DB FIELD
		                    unset($p['map'][$side]['file'][$i]); //MAPPING FIELD
		                }
		            }
		        }
		    }

				if($applyTemplate)//if predefind template is selected
				{

						$tempMappingData = unserialize($tempMappingXml);
						$parseXmlInter .= '    <var name="map">' . $nl;
						$mapXml .= '<action type="oopsprofile/dataflow_convert_mapper_column" method="map">' . $nl;
						$mapXml .= '    <var name="map">' . $nl;
						if($tempMappingXml!='')
						{
								foreach ($tempMappingData as $key => $value) {
								    $mapXml .= '        <map name="' . $key . '"><![CDATA[' . $value . ']]></map>' . $nl;
								    $parseXmlInter .= '        <map name="' . $value . '"><![CDATA[' . $key . ']]></map>' . $nl;
								}
						}
				    $mapXml .= '    </var>' . $nl;
						$parseXmlInter .= '    </var>' . $nl;
						$mapXml .= '</action>' . $nl . $nl;				

				}
				else
				{
						$enable = '';
						if($this->getEnableContent()!=NULL)
						{
								$enable = $this->getEnableContent();
						}
						else //saving from outside
						{
								$DataflowId = $this->getProfileId();
            		$oopsProfile = Mage::getModel('oopsprofile/oopsprofile')->getCollection()
																	->addFieldToFilter('dataflow_profile_id',$DataflowId)
																	->getFirstItem();
								$enable = $oopsProfile->getEnableContent();
						}


						if($p['parse']['type'] == 'xml' && $enable == 1)
						{
								$mapXml .= '<action type="oopsprofile/dataflow_convert_mapper_column" method="map">' . $nl;
								$mapXml .= '</action>' . $nl . $nl;
						}
						else
						{

									/**Apply Mapping**/
									if($this->getEntityType()!='customergroup')
									{
											$mapXml .= '<action type="dataflow/convert_mapper_column" method="map">' . $nl;
											$map = $p['map'][$this->getEntityType()];
											if (sizeof($map['db']) > 0) {
													$from = $map[$import ? 'file' : 'db'];
													$to = $map[$import ? 'db' : 'file'];
													$mapXml .= '    <var name="map">' . $nl;
													$parseXmlInter .= '    <var name="map">' . $nl;
													foreach ($from as $i => $f) {
														  $mapXml .= '        <map name="' . $f . '"><![CDATA[' . $to[$i] . ']]></map>' . $nl;
														  $parseXmlInter .= '        <map name="' . $f . '"><![CDATA[' . $to[$i] . ']]></map>' . $nl;
													}
													$mapXml .= '    </var>' . $nl;
													$parseXmlInter .= '    </var>' . $nl;
											}
											if ($p['map']['only_specified']) {
													$mapXml .= '    <var name="_only_specified">' . $p['map']['only_specified'] . '</var>' . $nl;
													//$mapXml .= '    <var name="map">' . $nl;
													$parseXmlInter .= '    <var name="_only_specified">' . $p['map']['only_specified'] . '</var>' . $nl;
											}
											$mapXml .= '</action>' . $nl . $nl;
									}
						}
				}

        $parsers = array(
            'product' => 'oopsprofile/product_convert_parser_product',
            'customer' => 'customer/convert_parser_customer',
            'customergroup' => 'oopsprofile/customergroup_convert_parser_customergroup',
            'order' => 'oopsprofile/order_convert_parser_order',
            'shipment' => 'oopsprofile/shipment_convert_parser_shipment',
            'invoice' => 'oopsprofile/invoice_convert_parser_invoice',
            'creditmemo' => 'oopsprofile/creditmemo_convert_parser_creditmemo',
        );

        if ($import) {

            $parseXmlInter .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
        } else {
            $parseDataXml = '<action type="' . $parsers[$this->getEntityType()] . '" method="unparse">' . $nl;
            $parseDataXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
            if (isset($p['export']['add_url_field'])) {
                $parseDataXml .= '    <var name="url_field"><![CDATA['
                        . $p['export']['add_url_field'] . ']]></var>' . $nl;
            }
            $parseDataXml .= '</action>' . $nl . $nl;
        }



        $adapters = array(
            'product' => 'oopsprofile/product_convert_adapter_product',
            'customer' => 'customer/convert_adapter_customer',
            'customergroup' => 'oopsprofile/customergroup_convert_adapter_customergroup',
            'order' => 'oopsprofile/order_convert_adapter_order',
            'shipment' => 'oopsprofile/shipment_convert_adapter_shipment',
            'invoice' => 'oopsprofile/invoice_convert_adapter_invoice',
            'creditmemo' => 'oopsprofile/creditmemo_convert_adapter_creditmemo',
        );

        if ($import) {
            $entityXml = '<action type="' . $adapters[$this->getEntityType()] . '" method="save">' . $nl;
            $entityXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
            $entityXml .= '</action>' . $nl . $nl;
        } else {
            $entityXml = '<action type="' . $adapters[$this->getEntityType()] . '" method="load">' . $nl;
            $entityXml .= '    <var name="store"><![CDATA[' . $this->getStoreId() . ']]></var>' . $nl;
            if($p['filter']['fromdate']!='')
            {
            		$entityXml .= '    <var name="fromdate">' . $p['filter']['fromdate'] . '</var>' . $nl;
            }
            if($p['filter']['todate']!='')
            {
            		$entityXml .= '    <var name="todate">' . $p['filter']['todate'] . '</var>' . $nl;
            }
            if(!empty($p['filter']['hiddentype']))
            {
            		$filters = $p['filter']['hiddentype'];
            		$count = count($filters);
            		$i = 1; 
            		$entityXml .= '    <var name="hiddentype">';
            		foreach($filters as $filter)
            		{
            				$entityXml .= $filter ;
            				if($count != $i++)
            				{
            						$entityXml .= ',' ;
            				}
            		}
            		$entityXml .= '</var>' . $nl;
            }
            $entityXml .= '</action>' . $nl . $nl;
        }

        // Need to rewrite the whole xml action format
        if ($import) {

            $xml = $setXml;
            $xml .= $parseXmlInter;

						$xml .= '    <var name="root_catalog_id"></var>' . $nl;
						$xml .= '    <var name="reimport_images"><![CDATA[true]]></var>' . $nl;
						$xml .= '    <var name="deleteall_andreimport_images"><![CDATA[true]]></var>' . $nl;
						$xml .= '    <var name="exclude_images"><![CDATA[false]]></var>' . $nl;
						$xml .= '    <var name="exclude_gallery_images"><![CDATA[false]]></var>' . $nl;
						$xml .= '    <var name="append_tier_prices"><![CDATA[true]]></var>' . $nl;
						$xml .= '    <var name="append_group_prices"><![CDATA[false]]></var>' . $nl;
						$xml .= '    <var name="append_categories"><![CDATA[false]]></var>' . $nl;

            
            $xml .= '    <var name="adapter">' . $adapters[$this->getEntityType()] . '</var>' . $nl;
            $xml .= '    <var name="method">parse</var>' . $nl;
            $xml .= '</action>';
        } else {
            $xml = $entityXml . $parseDataXml . $mapXml . $parseXml . $setXml;
        }

        $this->setGuiData($p);
        $this->setActionsXml($xml);

        /* echo "<pre>" . print_r($p,1) . "</pre>";
          echo "<xmp>" . $xml . "</xmp>";
          die; */
        return $this;
    }

    protected function generateApiXML($nl, $import, $p) {
        $apiXml = '<action type="oopsprofile/convert_adapter_api" method="'
                . ($import ? 'load' : 'save') . '">' . $nl;

        $apiXml .= '    <var name="entitytype">'. $this->getEntityType() .'</var>' . $nl;

        return $apiXml;
    }

    protected function generateHttpXML($nl, $import, $p) {
        $httpXml = '<action type="oopsprofile/convert_adapter_http" method="'
                . ($import ? 'load' : 'save') . '">' . $nl;

        $httpXml .= '    <var name="fileformat">' . $p['parse']['type'] . '</var>' . $nl;
        $httpXml .= '    <var name="entitytype">'. $this->getEntityType() .'</var>' . $nl;

        return $httpXml;
    }

    protected function generateEmailXML($nl, $import, $p) {

        $fileName = Mage::helper('oopsprofile/mail')->getFileNameWithPrefix($p['email']['mailsubject']);

        $emailXml = '<action type="oopsprofile/convert_adapter_mail" method="'. ($import ? 'load' : 'save') . '">' . $nl;

				if(isset($p['email']['service_provider']) && $p['email']['service_provider'] != null){

						$emailXml .= '    <var name="service_provider">' . $p['email']['service_provider'] . '</var>' . $nl;
        		$emailXml .= '    <var name="' . $p['email']['service_provider'] . '_client_id"><![CDATA[' . $p['email'][$p['email']['service_provider']]['client_id'] . ']]></var>' . $nl;
        		$emailXml .= '    <var name="' . $p['email']['service_provider'] . '_client_secret"><![CDATA[' . $p['email'][$p['email']['service_provider']]['client_secret'] . ']]></var>' . $nl;
        		$emailXml .= '    <var name="' . $p['email']['service_provider'] . '_redirect_uri"><![CDATA[' . $p['email'][$p['email']['service_provider']]['redirect_uri'] . ']]></var>' . $nl;
        		$emailXml .= '    <var name="' . $p['email']['service_provider'] . '_developer_key"><![CDATA[' . $p['email'][$p['email']['service_provider']]['developer_key'] . ']]></var>' . $nl;
				}
        
        $emailXml .= '    <var name="host">' . $p['email']['host'] . '</var>' . $nl;
        $emailXml .= '    <var name="port">' . $p['email']['port'] . '</var>' . $nl;
        $emailXml .= '    <var name="username"><![CDATA[' . $p['email']['username'] . ']]></var>' . $nl;
        $emailXml .= '    <var name="password"><![CDATA[' . $p['email']['pass'] . ']]></var>' . $nl;
        $emailXml .= '    <var name="mailsubject"><![CDATA[' . $p['email']['mailsubject'] . ']]></var>' . $nl;
        $emailXml .= '    <var name="emailto"><![CDATA[' . $p['email']['sendto'] . ']]></var>' . $nl;
        $emailXml .= '    <var name="attachmentsavepath"><![CDATA[' . $p['email']['attachmentsavepath'] . ']]></var>' . $nl;
        $emailXml .= '    <var name="deleteattachment"></var>' . $nl;
        $emailXml .= '    <var name="checkonlynewmsgs"></var>' . $nl;
        if (!$import) {
            $emailXml .= '    <var name="filename">' . $fileName . '.' . $p['parse']['type'] . '</var>' . $nl;
        }


        return $emailXml;
    }

    protected function generateFileXML($nl, $import, $p) {

    		//if($p['parse']['type']=='xml' || $p['parse']['type']=='xls' || $p['parse']['type']=='xlsx')
    		//{
						$fileXml = '<action type="oopsprofile/convert_adapter_io" method="'
                . ($import ? 'load' : 'save') . '">' . $nl;
       /* }
        else
        {
        		$fileXml = '<action type="dataflow/convert_adapter_io" method="'
                . ($import ? 'load' : 'save') . '">' . $nl;
        }*/
        
        $fileXml .= '    <var name="fileformat">' . $p['parse']['type'] . '</var>' . $nl;

        
        $fileXml .= '    <var name="type">' . $p['file']['type'] . '</var>' . $nl;
        $fileXml .= '    <var name="path">' . $p['file']['path'] . '</var>' . $nl;
        $fileXml .= '    <var name="filename"><![CDATA[' . $p['file']['filename'] . ']]></var>' . $nl;
        if ($p['file']['type'] === 'ftp') {
            $hostArr = explode(':', $p['file']['host']);
            $fileXml .= '    <var name="host"><![CDATA[' . $hostArr[0] . ']]></var>' . $nl;
            if (isset($hostArr[1])) {
                $fileXml .= '    <var name="port"><![CDATA[' . $hostArr[1] . ']]></var>' . $nl;
            }
            if (!empty($p['file']['passive'])) {
                $fileXml .= '    <var name="passive">true</var>' . $nl;
            }
            if ((!empty($p['file']['file_mode']))
                    && ($p['file']['file_mode'] == FTP_ASCII || $p['file']['file_mode'] == FTP_BINARY)
            ) {
                $fileXml .= '    <var name="file_mode">' . $p['file']['file_mode'] . '</var>' . $nl;
            }
            if (!empty($p['file']['user'])) {
                $fileXml .= '    <var name="user"><![CDATA[' . $p['file']['user'] . ']]></var>' . $nl;
            }
            if (!empty($p['file']['password'])) {
                $fileXml .= '    <var name="password"><![CDATA[' . $p['file']['password'] . ']]></var>' . $nl;
            }
        }

        if ($p['file']['type'] === 'sftp') {
            $hostArr = explode(':', $p['file']['host']);
            $fileXml .= '    <var name="host"><![CDATA[' . $hostArr[0] . ']]></var>' . $nl;
            if (isset($hostArr[1])) {
                $fileXml .= '    <var name="port"><![CDATA[' . $hostArr[1] . ']]></var>' . $nl;
            }
            if (!empty($p['file']['user'])) {
                $fileXml .= '    <var name="username"><![CDATA[' . $p['file']['user'] . ']]></var>' . $nl;
            }
            if (!empty($p['file']['password'])) {
                $fileXml .= '    <var name="password"><![CDATA[' . $p['file']['password'] . ']]></var>' . $nl;
            }
        }


        return $fileXml;
    }

}

