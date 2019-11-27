<?php

class PI_Oopsprofile_Adminhtml_OopsprofileController extends Mage_Adminhtml_Controller_Action
{

		protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
    }

		public function preDispatch()
		{

				parent::preDispatch();

				Mage::dispatchEvent('oopsprofile_create_api',array());

				/**
				* Save Installation Date in core_config_data
				**/

				$configSet = Mage::getStoreConfigFlag('oopsprofile/installation/date');

				if(!$configSet)//if data is not exist in config
				{

						$currentDate = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));

						$ConfigDataModel = Mage::getModel('core/config_data');

						$ConfigDataModel->setScope('default')
														->setScopeId(0)
														->setPath('oopsprofile/installation/date')
														->setValue($currentDate)
														->setId(Null)
														->save();

				}

				
        return $this;
		}

		protected function _initProfile($idFieldName = 'id')
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        $profileId = (int) $this->getRequest()->getParam($idFieldName);
        $profile = Mage::getModel('dataflow/profile');

        if ($profileId) {
            $profile->load($profileId);
            if (!$profile->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('The profile you are trying to save no longer exists'));
                $this->_redirect('*/*');
                return false;
            }
        }

        Mage::register('current_convert_profile', $profile);

        $id = Mage::registry('current_convert_profile')->getId();


				//set session for import
        Mage::getSingleton('core/session')->setTemplateProfileId($id);

        return $this;
    }

    /**
     * Profiles list action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('oopsimportexport');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import/Export'), Mage::helper('adminhtml')->__('Import/Export'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Profiles'), Mage::helper('adminhtml')->__('Profiles'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('opsprofile/adminhtml_oopsprofile_grid')->toHtml());
    }

    /**
     * Profile edit action
     */
    public function editAction()
    {

				$this->_initProfile();
        $this->loadLayout();

        $profile = Mage::registry('current_convert_profile');

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getConvertProfileData(true);

        //set oopsprofile data in registry
        $oopsprofile = Mage::getModel('oopsprofile/oopsprofile')->loadByProfile($profile);
        Mage::register('current_convert_oopsprofile', $oopsprofile);
         

        $ruleModel = Mage::getModel('oopsprofile/rule')->getConditions()->setJsFormObject('rule_conditions_fieldset');

        Mage::register('current_oops_rule', $ruleModel);

        if (!empty($data)) {
            $profile->addData($data);
        }

        $this->_title($profile->getId() ? $profile->getName() : $this->__('New Profile'));

        $this->_setActiveMenu('oopsimportexport');


        $this->_addContent(
            $this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile_edit', 'oopsprofile_edit')
        );

        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('oopsprofile/adminhtml_oopsprofile_edit_tabs'));

        //$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

   /**
   * Create new profile action
   */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save profile action
     */
    public function saveAction(){

        if ($data = $this->getRequest()->getPost()) {
        		

            if (!$this->_initProfile('profile_id')) {
                return;
            }

            $profile = Mage::registry('current_convert_profile');

            // Prepare profile saving data
            if (isset($data)) {
                $profile->addData($data);
            }

            try {
                $profile->save();

								$conditions = '';
								if(!empty($data['rule']))
								{
				            $data['conditions'] = $data['rule']['conditions'];
				            unset($data['rule']);

										$rulemodel = Mage::getModel('oopsprofile/rule');
				            $arr = $rulemodel->loadPost($data);
				            $conditions = $arr['conditions'][1];

				            //echo '<pre>';print_r($conditions);exit;

				            if(!empty($conditions))
										{
												$conditions = serialize($conditions);
										}
								}


								$dataflowId = $profile->getId();
								$oopsprofileModel = Mage::getModel('oopsprofile/oopsprofile');
								$oopsprofileId = $oopsprofileModel->getCollection()
																									->addFieldToFilter('dataflow_profile_id',$dataflowId)
																									->getFirstItem()
																									->getId();

								//echo $data['save_local_copy'];exit;	
								//echo $conditions;exit;

								/**if($data['save_local_copy']==1)
								{
										if($data['data_transfer']=='http' || $data['data_transfer']=='api' || $data['data_transfer']=='mail' || $data['direction']=='import')//if way type is api or http or direction is import
										{
												$data['save_local_copy']=0;
										}
								}**/

								/**if($data['save_products_separately']==1)
								{
										if($data['data_transfer']=='http' || $data['data_transfer']=='api' || $data['data_transfer']=='mail' || $data['direction']=='import')//if way type is api/http/mail or direction is import
										{
												$data['save_products_separately']=0;
										}
								}**/
								
								$oopsprofileModel->setDataflowProfileId($dataflowId)
																->setConditionsSerialized($conditions)
																//->setRunOnProductSave($data['run_on_product_save'])
																//->setRunOnCategorySave($data['run_on_category_save'])
																->setProfileEnable($data['profile_enable'])
																//->setSaveLocalCopy($data['save_local_copy'])
																->setProductsAllreadyDone($data['products_allready_done'])
																//->setSaveProductsSeparately($data['save_products_separately'])
																//->setXmlContent($data['xml_content'])
																//->setEnableContent($data['enable_content'])
																->setProfileDirection($data['direction']);


								



								

								
								//save data in oops profile	
								if($oopsprofileId==''){	                       
		              	$oopsprofileModel->save(); 
		            }
		            else{
		            		$oopsprofileModel->setId($oopsprofileId)->save(); 
		            }

								Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The profile has been saved.')
                );
            } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setConvertProfileData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id' => $profile->getId())));
                return;
            }
            if ($this->getRequest()->getParam('continue')) {
                $this->_redirect('*/*/edit', array('id' => $profile->getId()));
            } else {
                $this->_redirect('*/*');
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Invalid POST data (please check post_max_size and upload_max_filesize settings in your php.ini file).')
            );
            $this->_redirect('*/*');
        }
    }

    public function runAction()
    {

    		
    		$id = $this->getRequest()->getParams('id');
    		$oopsprofile = Mage::getModel('oopsprofile/oopsprofile')
											 ->getCollection()
											 ->addFieldToFilter('dataflow_profile_id',$id)
											 ->getFirstItem();
    		$isDisabled = $oopsprofile->getProfileEnable();
    		if($isDisabled == 1)//if profile is disable
    		{
    				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('The profile is disable.'));
    				
    				
						
						
						
        		$this->_redirect('*/*');
    		}
    		else
    		{
    				$this->_initProfile();
				    $this->loadLayout();
				    $this->renderLayout();
    		}
			
				 
    }

    
    /**
     * Delete profile action
     */
    public function deleteAction()
    {
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
        if ($profile->getId()) {
            try {
            		//delete from oops profile
            		$oopsprofileModel = Mage::getModel('oopsprofile/oopsprofile');
								$oopsprofileId = $oopsprofileModel->getCollection()->addFieldToFilter('dataflow_profile_id',$profile->getId())->getFirstItem()->getId();
								if($oopsprofileId!='')
								{	                 
		              $oopsprofileModel->setId($oopsprofileId)->delete(); 
		            }
		            
                $profile->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The profile has been deleted.'));
            } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*');
    }

    public function batchRunAction()
    {
        if ($this->getRequest()->isPost()) {
            $batchId = $this->getRequest()->getPost('batch_id', 0);
            $rowIds  = $this->getRequest()->getPost('rows');

            /* @var $batchModel Mage_Dataflow_Model_Batch */
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);

            if (!$batchModel->getId()) {
                return;
            }
            if (!is_array($rowIds) || count($rowIds) < 1) {
                return;
            }
            if (!$batchModel->getAdapter()) {
                return;
            }

            $batchImportModel = $batchModel->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();

            $adapter = Mage::getModel($batchModel->getAdapter());
            $adapter->setBatchParams($batchModel->getParams());

            $errors = array();
            $saved  = 0;
            foreach ($rowIds as $importId) {
                $batchImportModel->load($importId);
                if (!$batchImportModel->getId()) {
                    $errors[] = Mage::helper('dataflow')->__('Skip undefined row.');
                    continue;
                }

                try {
                    $importData = $batchImportModel->getBatchData();
                    $adapter->saveRow($importData);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
                $saved ++;
            }

            if (method_exists($adapter, 'getEventPrefix')) {
                /**
                 * Event for process rules relations after products import
                 */
                Mage::dispatchEvent($adapter->getEventPrefix() . '_finish_before', array(
                    'adapter' => $adapter
                ));

                /**
                 * Clear affected ids for adapter possible reuse
                 */
                $adapter->clearAffectedEntityIds();
            }

            $result = array(
                'savedRows' => $saved,
                'errors'    => $errors
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function batchFinishAction()
    {
        $batchId = $this->getRequest()->getParam('id');
        if ($batchId) {
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if ($batchModel->getId()) {
                $result = array();
                try {
                    $batchModel->beforeFinish();
                } catch (Mage_Core_Exception $e) {
                    $result['error'] = $e->getMessage();
                } catch (Exception $e) {
                    $result['error'] = Mage::helper('adminhtml')->__('An error occurred while finishing process. Please refresh the cache');
                }
                $batchModel->delete();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }

		/**Download File**/
    public function downloadFileAction()
		{	
				$data = $this->getRequest()->getParams();		
				$filepath = $data['filepath'];
				$id = $data['profileid'];
				$filetype = $data['fileType'];
				if($filetype == 'ftp' || $filetype == 'sftp')
				{
						$filepath = $this->saveOnLocalFromFtp($data);
				} 

				
				if($filepath!='')
				{
						if(file_exists($filepath))
						{
								$responce = $this->getResponse();
								$responce->setHttpResponseCode( 200 );
								$responce->setHeader( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true );
								$responce->setHeader( 'Pragma', 'public', true );
								$responce->setHeader( 'Content-type', 'application/force-download' );
								$responce->setHeader( 'Content-Length', filesize($filepath) );
								$responce->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($filepath) );
								$responce->clearBody();
								$responce->sendHeaders();
								readfile( $filepath );
								return;
						}
						 else
						 {
						 		Mage::getSingleton('adminhtml/session')->addError(
				            $this->__('No file exist for current profile')
				        );
				        $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id' => $id)));
						 }
				 }
				 else
				 {
				 		Mage::getSingleton('adminhtml/session')->addError(
                $this->__('file path and name is not defined')
            );
            $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id' => $id)));
				 }
		}

		//save file fron ftp to local server
		public function saveOnLocalFromFtp($data)
    {
    		$filepath = $data['filepath'];
    		$path = 'var/export/ftp';
    		$file = $path.$filepath;//temp file path on local server
    		$exported = explode('/',$file);
    		$filename = $exported[count($exported)-1];
    		unset($exported[count($exported)-1]);

    		
    		
				$remote_file = $filepath;//remote file path

				//create path if not exist
				$tempPath = implode('/',$exported);
				if (!file_exists($tempPath)) {
						mkdir($tempPath, 0777, true);
				}

				// set up basic connection
				$conn_id = ftp_connect($data['host']);

				// login with username and password
				$login_result = ftp_login($conn_id, $data['user'], $data['pass']);

				
				$fileMode = FTP_BINARY;
				

				if (ftp_get($conn_id, $file,$remote_file, $fileMode)) {
				$message = Mage::helper('dataflow')->__('successfully loaded '). $remote_file;
				//$this->addException($message);
				} else {
				 $message = Mage::helper('dataflow')->__('There was a problem while loading '). $remote_file;
				 Mage::getSingleton('adminhtml/session')->addError(
            $this->__('file path / name is not defined')
        	);
        	$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id' => $id)));
				}
				// close the connection and the file handler
				ftp_close($conn_id);

				return $file;
    }


    /**
     * Customer orders grid
     *
     */
    public function historyAction() {
        $this->_initProfile();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml()
        );
    }

/*
  public function authenticateTokenAction(){
				if (isset($_GET['code'])) {
					echo '<pre>';
					$client = Mage::getSingleton('core/session')->getGmailClient();
					var_dump($client);
  				$client->authenticate($_GET['code']);
  				Mage::getSingleton('core/session')->setGmailToken($client->getAccessToken());
  				var_dump(Mage::getSingleton('core/session')->getGmailToken());
  				
				}
		}  
*/
}

