<?php 

class PI_Oopsprofile_Helper_Table extends Mage_Core_Helper_Abstract
{

		

		//return column names of table
		protected function getTableColumns($Table,$prefix = '')
		{
				$orderTable = Mage::getSingleton('core/resource')->getTableName($Table);

				$read = Mage::getSingleton('core/resource')->getConnection('core_read');

				$columnsDeatil = $read->describeTable($orderTable);//column descriptions for a table

				$columns = array_keys($columnsDeatil);//fetch array keys

				if($prefix!='')
				{
						foreach($columns as $col)
						{
								$newcol[] = $prefix.$col;
						}
						$columns = $newcol;
				}				
				$columns = array_combine($columns, $columns);//assign array values to its keys

				return $columns;
		}

		/**
		** Return order field names **
		**/
		public function getOrderFields()
		{

				//fetch columns from order table
				$orderColumns = $this->getTableColumns('sales/order');

				$unsetOrderKeys = array('protect_code',
																'remote_ip',
																'x_forwarded_for',
																'edit_increment',
																'email_sent',
																'forced_shipment_with_incoice',
																'payment_authorization_amount',
																'payment_authorization_expiration',
																'forced_do_shipment_with_invoice',
																'base_shipping_hidden_tax_amount',
																'store_name'
																);

				foreach($unsetOrderKeys as $unsetOrderKey)
				{											
						unset($orderColumns[$unsetOrderKey]);
				}

				//columns for billing and shipping address

				$orderColumns['orderbilling_region_id'] = 'orderbilling_region_id';
				$orderColumns['orderbilling_region'] = 'orderbilling_region';
				$orderColumns['orderbilling_postcode'] = 'orderbilling_postcode';
				$orderColumns['orderbilling_street'] = 'orderbilling_street';
				$orderColumns['orderbilling_city'] = 'orderbilling_city';
				$orderColumns['orderbilling_country_id'] = 'orderbilling_country_id';
				$orderColumns['orderbilling_prefix'] = 'orderbilling_prefix';
				$orderColumns['orderbilling_firstname'] = 'orderbilling_firstname';
				$orderColumns['orderbilling_middlename'] = 'orderbilling_middlename';
				$orderColumns['orderbilling_lastname'] = 'orderbilling_lastname';
				$orderColumns['orderbilling_suffix'] = 'orderbilling_suffix';
				$orderColumns['orderbilling_email'] = 'orderbilling_email';
				$orderColumns['orderbilling_telephone'] = 'orderbilling_telephone';

				$orderColumns['ordershipping_region_id'] = 'ordershipping_region_id';
				$orderColumns['ordershipping_region'] = 'ordershipping_region';
				$orderColumns['ordershipping_postcode'] = 'ordershipping_postcode';
				$orderColumns['ordershipping_street'] = 'ordershipping_street';
				$orderColumns['ordershipping_city'] = 'ordershipping_city';
				$orderColumns['ordershipping_country_id'] = 'ordershipping_country_id';
				$orderColumns['ordershipping_prefix'] = 'ordershipping_prefix';
				$orderColumns['ordershipping_firstname'] = 'ordershipping_firstname';
				$orderColumns['ordershipping_middlename'] = 'ordershipping_middlename';
				$orderColumns['ordershipping_lastname'] = 'ordershipping_lastname';
				$orderColumns['ordershipping_suffix'] = 'ordershipping_suffix';
				$orderColumns['ordershipping_email'] = 'ordershipping_email';
				$orderColumns['ordershipping_telephone'] = 'ordershipping_telephone';


				//column for order payment
				$orderColumns['orderpayment_method'] = 'orderpayment_method';


				//column for comments
				$orderColumns['comment_data'] = 'comment_data';

				//fetch column for order items
				$itemColumns = $this->getTableColumns('sales/order_item','orderitem_');

				$unsetItemKeys = array('orderitem_item_id',
															'orderitem_order_id',
															'orderitem_store_id',
															'orderitem_created_at',
															'orderitem_updated_at',
															'orderitem_applied_rule_ids',
															'orderitem_additional_data',
															'orderitem_is_virtual'
															);
				foreach($unsetItemKeys as $unsetItemKey)
				{											
						unset($itemColumns[$unsetItemKey]);
				}

				$orderColumns = array_merge($orderColumns,$itemColumns);
					
				return $orderColumns;				
		}


		/**
		** Return Invoice field names **
		**/
		public function getInvoiceFields()
		{
				//fetch columns from invoice table
				$invoiceColumns = $this->getTableColumns('sales/invoice');

				foreach($invoiceColumns as $key=>$value)
				{
						if($key=='increment_id')
						{
								$invoiceColumns['invoice_increment_id']='invoice_increment_id';
								unset($invoiceColumns['increment_id']);
								break;
						}									
				}

				//column for comments
				$invoiceColumns['comment_data'] = 'comment_data';

				//column for grid data
				$invoiceColumns['order_increment_id'] = 'order_increment_id';
				$invoiceColumns['invoicegrid_order_created_at'] = 'invoicegrid_order_created_at';
				$invoiceColumns['invoicegrid_billing_name'] = 'invoicegrid_billing_name';

				//fetch columns for items
				$itemColumns = $this->getTableColumns('sales/invoice_item','invoiceitem_');

				$unsetItemKeys = array('invoiceitem_entity_id',
															'invoiceitem_parent_id',
															'invoiceitem_order_item_id'
															);

				foreach($unsetItemKeys as $unsetItemKey)
				{											
						unset($itemColumns[$unsetItemKey]);
				}

				$invoiceColumns = array_merge($invoiceColumns,$itemColumns);

				return $invoiceColumns;
		}

		/**
		** Return Shipment field names **
		**/
		public function getShipmentFields()
		{
				//fetch columns from shipment table
				$shipmentColumns = $this->getTableColumns('sales/shipment');

				foreach($shipmentColumns as $key=>$value)
				{
						if($key=='increment_id')
						{
								$shipmentColumns['shipment_increment_id']='shipment_increment_id';
								unset($shipmentColumns['increment_id']);
								break;
						}									
				}

				//column for comments
				$shipmentColumns['comment_data'] = 'comment_data';

				//column for track data
				$shipmentColumns['track_data'] = 'track_data';

				//column for grid data
				$shipmentColumns['order_increment_id'] = 'order_increment_id';
				$shipmentColumns['shipmentgrid_order_created_at'] = 'shipmentgrid_order_created_at';
				$shipmentColumns['shipmentgrid_shipping_name'] = 'shipmentgrid_shipping_name';

				//fetch columns for items
				$itemColumns = $this->getTableColumns('sales/shipment_item','shipmentitem_');

				$unsetItemKeys = array('shipmentitem_entity_id',
															'shipmentitem_parent_id',
															'shipmentitem_order_item_id'
															);

				foreach($unsetItemKeys as $unsetItemKey)
				{											
						unset($itemColumns[$unsetItemKey]);
				}

				$shipmentColumns = array_merge($shipmentColumns,$itemColumns);

				return $shipmentColumns;
		}


		/**
		** Return Creditmemo field names **
		**/
		public function getCreditmemoFields()
		{
				//fetch columns from creditmemo table
				$creditmemoColumns = $this->getTableColumns('sales/creditmemo');

				foreach($creditmemoColumns as $key=>$value)
				{
						if($key=='increment_id')
						{
								$creditmemoColumns['creditmemo_increment_id']='creditmemo_increment_id';
								unset($creditmemoColumns['increment_id']);
								break;
						}									
				}

				//column for comments
				$creditmemoColumns['comment_data'] = 'comment_data';

				//column for grid data
				$creditmemoColumns['order_increment_id'] = 'order_increment_id';
				$creditmemoColumns['creditmemogrid_order_created_at'] = 'creditmemogrid_order_created_at';
				$creditmemoColumns['creditmemogrid_billing_name'] = 'creditmemogrid_billing_name';

				//fetch columns for items
				$itemColumns = $this->getTableColumns('sales/creditmemo_item','creditmemoitem_');

				$unsetItemKeys = array('creditmemoitem_entity_id',
															'creditmemoitem_parent_id',
															'creditmemoitem_order_item_id'
															);

				foreach($unsetItemKeys as $unsetItemKey)
				{											
						unset($itemColumns[$unsetItemKey]);
				}

				$creditmemoColumns = array_merge($creditmemoColumns,$itemColumns);

				return $creditmemoColumns;
		}
}
