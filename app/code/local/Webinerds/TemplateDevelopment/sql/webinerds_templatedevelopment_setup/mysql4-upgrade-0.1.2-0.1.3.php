<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('catalog_product_entity_media_gallery_value'), 'page', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => false,
        'comment' => 'page'
    ));
$installer->endSetup();