<?php

class PI_Oopsprofile_Model_Mail extends Zend_Mail{

		
	

		public function sendMailWithAttachment($config){

			$filename = $config['filename'];
			$filepath = $config['path'];
			$storeId = Mage::app()->getStore()->getId();
			
			$timestamp = Mage::app()->getLocale()->storeDate($storeId);

			$mailTo = explode(',', $config['emailto']);
			if(!empty($mailTo)){
				$serviceProvider = isset($config['service_provider']) ? $config['service_provider'] : null;
				switch($serviceProvider){
					case 'gmail':
						 $email = $config['username']. '@' .'gmail.com';
						break;
					default:
						$email = $config['username'];
				}

				$this->addTo($mailTo);
				$this->setFrom($email);
				$this->setSubject($config['mailsubject']);
				$this->setBodyHtml(sprintf('File generated at: %s </br> Filename: %s', $timestamp, $filename));
				$this->addFileAsAttachment($filename, $filepath);
				try{
					$this->send();
				}catch(Exception $e){
					Mage::log($e);
					Mage::throwException($e->getMessage());
				}
			}
		}

		protected function addFileAsAttachment($filename, $filepath){

			$fullPath = $filepath . DS . $filename ;
			$filedata = file_get_contents($fullPath);
			$this->createAttachment(
								$filedata, 
								Zend_Mime::TYPE_OCTETSTREAM, 
								Zend_Mime::DISPOSITION_ATTACHMENT,
								Zend_Mime::ENCODING_BASE64, 
								$filename
			);
			return $this;
		}
}
