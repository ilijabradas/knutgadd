<?php
class PI_Oopsprofile_Model_CronObserver extends Varien_Object{

	const CRON_MODEL_PATH   = 'crontab/jobs/oopsprofile_cron/run/model';
	const PROFILE_JOB_CODE_PREFIX = 'oopsprofile_cron_';
	const OOPS_PROFILE_LOG_FILE = 'oops.log';
	
	/*
	 * Save Cron Schedule to Core_Config_Data
	 */
	public function saveCronSchedule($observer){

		$oopsprofile = $observer->getEvent()->getOopsprofile();

		$cronExprKeyPath = 'crontab/jobs/oopsprofile_cron_' . $oopsprofile->getId() . '/schedule/cron_expr';
		$cronModelKeyPath =  'crontab/jobs/oopsprofile_cron_' . $oopsprofile->getId() . '/run/model';
		// 0 means profile is enabled
		$isEnabled = $oopsprofile->getData('profile_enable') == 0;

		if($oopsprofile->getData('cron_enabled') == 1 && $isEnabled){

			$cronExprString = '';
			$time = '';
			$frequency = '';

			$frequencyDaily     = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
      $frequencyWeekly    = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
      $frequencyMonthly   = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

			$time = $oopsprofile->getData('cron_start_time');
			$frequency = $oopsprofile->getData('cron_frequency');

			$isExpertMode = $oopsprofile->getData('cron_expert_mode');

			if($time && $frequency || $isExpertMode){

				$cronDayOfWeek = date('N');
		    $cronExprArray = array(
		        intval($time[1]),                                   # Minute
		        intval($time[0]),                                   # Hour
		        ($frequency == $frequencyMonthly) ? '1' : '*',       # Day of the Month
		        '*',                                                # Month of the Year
		        ($frequency == $frequencyWeekly) ? '1' : '*',        # Day of the Week
		    );
		    $cronExprString = join(' ', $cronExprArray);

		    $cronModelValue = 'oopsprofile/cronObserver::runProfile';

				if($isExpertMode){
					$cronExprString = $oopsprofile->getData('cron_expression');
				}

				try {
		        Mage::getModel('core/config_data')
		            ->load($cronExprKeyPath, 'path')
		            ->setValue($cronExprString)
		            ->setPath($cronExprKeyPath)
		            ->save();

		        Mage::getModel('core/config_data')
		            ->load($cronModelKeyPath, 'path')
		            ->setValue($cronModelValue)
		            ->setPath($cronModelKeyPath)
		            ->save();
		    }catch (Exception $e) {
		        Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the cron expression.'));
		    }
			}

		}else{
				try {
		      $configExpr = Mage::getModel('core/config_data')->load($cronExprKeyPath, 'path');
					if($configExpr){
						$configExpr->delete();
					}
					
					$configModel = Mage::getModel('core/config_data')->load($cronModelKeyPath, 'path');
					if($configModel){
						$configModel->delete();
					}
	        
		    }catch (Exception $e) {
		        Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the cron expression.'));
		    }
		}

		if($isEnabled!=1)
		{
				$msg = Mage::helper('oopsprofile')->__('You can shedule the cron after enabling the profile');
				Mage::getSingleton('adminhtml/session')->addNotice($msg);
		}

		return $this;
	}


	/*
	 * Save Oops Cron Schedule to Core_Config_Data
	 */
	public function saveOopsCronSchedule($observer){

		$oopsprofile = $observer->getEvent()->getOopsprofile();

		$cronExprKeyPath = 'crontab/jobs/oopsprofile_cron_' . $oopsprofile->getId() . '/schedule/cron_expr';
		$cronModelKeyPath =  'crontab/jobs/oopsprofile_cron_' . $oopsprofile->getId() . '/run/model';
		// 0 means profile is enabled
		$isEnabled = $oopsprofile->getData('profile_enable') == 0;

		if($oopsprofile->getData('cron_enabled') == 1 && $isEnabled){

			$cronExprString = $oopsprofile->getData('cron_expression');

			
					$cronModelValue = 'oopsprofile/cronObserver::runProfile';

		
					try {
							Mage::getModel('core/config_data')
									->load($cronExprKeyPath, 'path')
									->setValue($cronExprString)
									->setPath($cronExprKeyPath)
									->save();

							Mage::getModel('core/config_data')
									->load($cronModelKeyPath, 'path')
									->setValue($cronModelValue)
									->setPath($cronModelKeyPath)
									->save();
					}catch (Exception $e) {
							Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the cron expression.'));
					}
				

		}else{
				try {
		      $configExpr = Mage::getModel('core/config_data')->load($cronExprKeyPath, 'path');
					if($configExpr){
						$configExpr->delete();
					}
					
					$configModel = Mage::getModel('core/config_data')->load($cronModelKeyPath, 'path');
					if($configModel){
						$configModel->delete();
					}
	        
		    }catch (Exception $e) {
		        Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the cron expression.'));
		    }
		}

		if($isEnabled!=1)
		{
				$msg = Mage::helper('oopsprofile')->__('You can shedule the cron after enabling the profile');
				Mage::getSingleton('adminhtml/session')->addNotice($msg);
		}

		return $this;
	}

	/*
	 * delete cron schedule from core_config_data when related oopsprofile is deleted
	 **/
	public function deleteCronSchedule($observer){

		$oopsprofile = $observer->getEvent()->getOopsprofile();

		//$oopsProfileId = $oopsprofile->getId(); 

		//if($oopsprofile->getData('cron_enabled') == 1){
			$cronExprKeyPath = 'crontab/jobs/oopsprofile_cron_' . $oopsprofile->getId() . '/schedule/cron_expr';
			$cronModelKeyPath =  'crontab/jobs/oopsprofile_cron_' . $oopsprofile->getId() . '/run/model';

			try{
	        $configExpr = Mage::getModel('core/config_data')->load($cronExprKeyPath, 'path');
					$configExpr->delete();

	        $configModel = Mage::getModel('core/config_data')->load($cronModelKeyPath, 'path');
	        $configModel->delete();

	    }catch (Exception $e) {
	        Mage::throwException(Mage::helper('adminhtml')->__('Unable to delete the cron expression.'));
	    }
		//}

		return $this;
	}



	

	protected function _initOopsProfile($id){

		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

		$userModel = Mage::getModel('admin/user');
		$userModel->setUserId(0);
		Mage::getSingleton('admin/session')->setUser($userModel);
				
		$oopsprofile = Mage::getModel('oopsprofile/oopsprofile')->load($id);

		$dataflowProfileId = $oopsprofile->getData('dataflow_profile_id');  
		$dataflowProfile = Mage::getModel('dataflow/profile')->load($dataflowProfileId);
		if (!$dataflowProfile->getId()) {
    	Mage::log('ERROR: Incorrect profile id', null, self::OOPS_PROFILE_LOG_FILE);
		}

		Mage::unregister('current_convert_profile');
		Mage::unregister('current_convert_oopsprofile');	

		//Mage::register('current_convert_profile', $dataflowProfile);
    Mage::register('current_convert_oopsprofile', $oopsprofile);

    return $dataflowProfile;
	}	

	/*Run Oopsprofile Via Cron*/	
	public function runProfile($schedule){

			$jobCode = $schedule->getData('job_code');
			$pos = strpos((string)$jobCode, self::PROFILE_JOB_CODE_PREFIX);
			$strLen = strlen(self::PROFILE_JOB_CODE_PREFIX);

			$logFileName = self::OOPS_PROFILE_LOG_FILE;

			//Re-confirm that its a oops profile
			if(substr($jobCode, 0,$strLen) == self::PROFILE_JOB_CODE_PREFIX){

				$profileId = substr($jobCode, $strLen);

				//load & set oopsprofile & dataflow profile to registry
				$profile = $this->_initOopsProfile($profileId);

				Mage::log('Oopsprofile:: ' . $profileId . ' Started.', null, $logFileName);

				//try{
					
					Mage::log('Is Cron Disabled:: '. Mage::registry('current_convert_oopsprofile')->getData('cron_enable'), null, $logFileName);
					if(Mage::registry('current_convert_oopsprofile')->getData('cron_enable') == 1){
						Mage::log('Halting Execution:: profile disabled.', null, $logFileName);
						return false;
					}

					//$profile = Mage::registry('current_convert_profile');
					$profile->run();

					$batchModel = Mage::getSingleton('dataflow/batch');
					if ($batchModel->getId()) {
		          if ($batchModel->getAdapter()) {

									$batchId = $batchModel->getId(); 
		              $batchImportModel = $batchModel->getBatchImportModel();
		              $importIds = $batchImportModel->getIdCollection();

									Mage::log('Ids to import::', null, $logFileName);
									Mage::log($importIds, null, $logFileName);

									$batchModel = Mage::getModel('dataflow/batch')->load($batchId);      
		    					$adapter = Mage::getModel($batchModel->getAdapter());

		              $recordCount = 0;
		              foreach ($importIds as $importId) {

										$recordCount++;
										try{

											$batchImportModel->load($importId);
											if (!$batchImportModel->getId()) {
												 $errors[] = Mage::helper('dataflow')->__('Skip undefined row');
												 continue;
											}

											$importData = $batchImportModel->getBatchData();
											try {
												$adapter->saveRow($importData);
											} catch (Exception $e) {
												Mage::log($e->getMessage(),null,$logFileName);          
												continue;
											}
						
											if ($recordCount%20 == 0) {
												Mage::log($recordCount . ' - Completed!!',null,$logFileName);
											}

										} catch(Exception $ex) {
											Mage::log('Record# ' . $recordCount . ' - SKU = ' . $importData['sku']. ' - Error - ' . $ex->getMessage(),null,$logFileName);        
										}
									}

									if(!empty($errors)){
										Mage::log('Rows Skipped::', null, $logFileName);
										Mage::log($errors, null, $logFileName);
									}
		          } else {
		              Mage::log('setBatchModelHasAdapter::false', null, $logFileName);
									Mage::log('Export '.$profileId.' Complete. BatchID: '.$batchModel->getId(), null, $logFileName);	
		              $batchModel->delete();
		          }
		      }	
				//}catch(Exception $e){
				//	Mage::log($e, null, $logFileName);
				//}
			}
	}

	public function heartbeat($observer){
		return true;	
	}

}
