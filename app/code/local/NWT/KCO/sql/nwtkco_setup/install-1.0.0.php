<?php
/**
 * Klarna Checkout extension
 *
 * @category    NWT
 * @package     NWT_KCO
 * @copyright   Copyright (c) 2015 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     http://nordicwebteam.se/licenses/nwtcl/1.0.txt  NWT Commercial License (NWTCL 1.0)
 * 
 *
 */

/**
 * install script
 */

/* var $this Mage_Sales_Model_Resource_Setup */
$this->startSetup ();

/* var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $this->getConnection();


//add this fiels in <fieldsets><sales_convert_quote> in config.xml (to copy from quote to order, when save)

//Need by push action
$quoteData   = array(
    'nwt_reservation'=>'TEXT',
);

$paymentData = array(
    'klarna_reservation'=>'VARCHAR(255) NULL',
    'klarna_id'         =>'VARCHAR(255) NULL',
    'klarna_test'       =>'TINYINT(1) UNSIGNED NULL',
    'klarna_expires_at' =>'DATETIME NULL'
);

$addressData = array(
    'care_of' => 'VARCHAR(255) NULL'
);

$alterTables = array(
    'sales/quote'=>$quoteData,
    'sales/order'=>$quoteData,
    'sales/order_payment'=>$paymentData,
    'sales/quote_payment'=>$paymentData,
    'sales/quote_address'=>$addressData,
    'sales/order_address'=>$addressData
);

foreach($alterTables as $table=>$columns) {

    $table = $this->getTable($table);

    foreach($columns as $column=>$definition) {
        $connection->addColumn($table,$column,$definition); ////@see lib/Varien/Db/Adapter/Pdo/Mysql.php
        //Actually, we need to use Mage_Sales_Model_Resource_Setup::addAttribute to add this atttributes, but...
        //all this tables are converted (long time ago) to the flat one, so, will works (trust me, I'm engineer, "merge si asa")
    }
}



//add 'care_of' as customer attribute

/* @var $customerSetup Mage_Customer_Model_Resource_Setup */

$customerSetup = Mage::getResourceModel('customer/setup', 'customer_setup');

$customerSetup->addAttribute('customer_address', 'care_of', array(
        'label'     => 'Care of',
        'type'      => Varien_Db_Ddl_Table::TYPE_VARCHAR,
        'length'    => 255,
        'input'     => 'text',
        'visible'   => true,
        'required'  => false
));

//$order = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'company')->getSortOrder() - 5;
$order = 55;

Mage::getSingleton('eav/config')->getAttribute('customer_address', 'care_of')
    ->setData('used_in_forms',array('adminhtml_customer_address','customer_address_edit','customer_register_address'))
    ->setSortOrder($order)
    ->save();

$this->endSetup ();

