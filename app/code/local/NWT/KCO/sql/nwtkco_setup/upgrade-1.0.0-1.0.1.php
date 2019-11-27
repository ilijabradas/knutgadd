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
 * upgrade script
 */

/** @var $installer Mage_Sales_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

/* var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $this->getConnection();



$alterTables = array('sales/quote','sales/order');
$column = 'nwt_reservation';

$definition  = array(
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 255,
    'nullable'  => true,
    'comment'   => 'Klarna Reservation.'
);




foreach($alterTables as $_table) {
    $table = $this->getTable($_table);
    $connection->modifyColumn($table,$column,$definition); ////@see lib/Varien/Db/Adapter/Pdo/Mysql.php
    $connection->addIndex($table, $installer->getIdxName($_table, array($column)), array($column));
}

$installer->endSetup ();

