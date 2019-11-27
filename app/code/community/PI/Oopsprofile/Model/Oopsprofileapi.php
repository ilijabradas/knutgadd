<?php
class PI_Oopsprofile_Model_Oopsprofileapi extends Mage_Api_Model_Resource_Abstract
{

		public function exportproductdata($request)
		{
				try
				{
						$filename = Mage::getBaseDir().'/apiexport/export_product_'.$request['profileid'].'.xml';
						if(glob($filename))
						{
								$xmlInstance = new Varien_Simplexml_Config($filename);
								$xml = $xmlInstance->getNode()->asNiceXml();
								return $xml;
						}
						else
						{
								return "<error>".Mage::helper('oopsprofile')->__('No Data Exist For Current Details')."</error>";
						}
				}

				catch (Exception $e) 
				{
						// fault with the code tag data_invalid
						$this->_fault('data_invalid', $e->getMessage());
				}
		}
		public function exportcustomerdata($request)
		{
				try
				{
						$filename = Mage::getBaseDir().'/apiexport/export_customer_'.$request['profileid'].'.xml';
						if(glob($filename))
						{
								$xmlInstance = new Varien_Simplexml_Config($filename);
								$xml = $xmlInstance->getNode()->asNiceXml();
								return $xml;
						}
						else
						{
								return "<error>".Mage::helper('oopsprofile')->__('No Data Exist For Current Details')."</error>";
						}
				}

				catch (Exception $e) 
				{
						// fault with the code tag data_invalid
						$this->_fault('data_invalid', $e->getMessage());
				}
		}
		public function exportcustomergroupdata($request)
		{
				try
				{
						$filename = Mage::getBaseDir().'/apiexport/export_customergroup_'.$request['profileid'].'.xml';
						if(glob($filename))
						{
								$xmlInstance = new Varien_Simplexml_Config($filename);
								$xml = $xmlInstance->getNode()->asNiceXml();
								return $xml;
						}
						else
						{
								return "<error>".Mage::helper('oopsprofile')->__('No Data Exist For Current Details')."</error>";
						}
				}

				catch (Exception $e) 
				{
						// fault with the code tag data_invalid
						$this->_fault('data_invalid', $e->getMessage());
				}
		}
		public function exportorderdata($request)
		{
				try
				{
						$filename = Mage::getBaseDir().'/apiexport/export_order_'.$request['profileid'].'.xml';
						if(glob($filename))
						{
								$xmlInstance = new Varien_Simplexml_Config($filename);
								$xml = $xmlInstance->getNode()->asNiceXml();
								return $xml;
						}
						else
						{
								return "<error>".Mage::helper('oopsprofile')->__('No Data Exist For Current Details')."</error>";
						}
				}

				catch (Exception $e) 
				{
						// fault with the code tag data_invalid
						$this->_fault('data_invalid', $e->getMessage());
				}
		}
		public function exportshipmentdata($request)
		{
				try
				{
						$filename = Mage::getBaseDir().'/apiexport/export_shipment_'.$request['profileid'].'.xml';
						if(glob($filename))
						{
								$xmlInstance = new Varien_Simplexml_Config($filename);
								$xml = $xmlInstance->getNode()->asNiceXml();
								return $xml;
						}
						else
						{
								return "<error>".Mage::helper('oopsprofile')->__('No Data Exist For Current Details')."</error>";
						}
				}

				catch (Exception $e) 
				{
						// fault with the code tag data_invalid
						$this->_fault('data_invalid', $e->getMessage());
				}
		}
		public function exportinvoicedata($request)
		{
				try
				{
						$filename = Mage::getBaseDir().'/apiexport/export_invoice_'.$request['profileid'].'.xml';
						if(glob($filename))
						{
								$xmlInstance = new Varien_Simplexml_Config($filename);
								$xml = $xmlInstance->getNode()->asNiceXml();
								return $xml;
						}
						else
						{
								return "<error>".Mage::helper('oopsprofile')->__('No Data Exist For Current Details')."</error>";
						}
				}

				catch (Exception $e) 
				{
						// fault with the code tag data_invalid
						$this->_fault('data_invalid', $e->getMessage());
				}
		}
		public function exportcreditmemodata($request)
		{
				try
				{
						$filename = Mage::getBaseDir().'/apiexport/export_creditmemo_'.$request['profileid'].'.xml';
						if(glob($filename))
						{
								$xmlInstance = new Varien_Simplexml_Config($filename);
								$xml = $xmlInstance->getNode()->asNiceXml();
								return $xml;
						}
						else
						{
								return "<error>".Mage::helper('oopsprofile')->__('No Data Exist For Current Details')."</error>";
						}
				}

				catch (Exception $e) 
				{
						// fault with the code tag data_invalid
						$this->_fault('data_invalid', $e->getMessage());
				}
		}

}
