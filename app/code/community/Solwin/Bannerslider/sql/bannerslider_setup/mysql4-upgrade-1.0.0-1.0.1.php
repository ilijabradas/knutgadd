<?php
/**
 * Created by PhpStorm.
 * User: WOLF
 * Date: 12/5/2018
 * Time: 1:16 PM
 */
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('solwinbannerslider'),
        'image_mobile',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'default' => null,
            'comment' => 'Background Image for mobile'
        )
    );

$installer->endSetup();