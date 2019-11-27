
<?php

 
$installer = $this;
$connection = $installer->getConnection();
 
$installer->startSetup();
 
$installer->getConnection()
    ->addColumn($installer->getTable('storelocator'),
    'store_position',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INT
        'nullable' => true,
        'default' => null,
        'comment' => 'Store position'
    )
);
 
$installer->endSetup();
