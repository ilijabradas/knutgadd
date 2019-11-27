<?php
/**
 * Created by PhpStorm.
 * User: WOLF
 * Date: 6/14/2019
 * Time: 1:31 AM
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->addColumn($installer->getTable('bannerslider/bannerslider'),
      'store_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 63,
            'nullable' => true,
            'default' => null,
            'comment' => ' Store View Selector'
        )
    );


$installer->endSetup();
