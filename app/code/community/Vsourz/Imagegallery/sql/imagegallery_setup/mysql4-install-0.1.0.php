<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('imagedetail'))
    ->addColumn('imagedetail_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Image ID')
    ->addColumn('image_title', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		'nullable'  => false,
        ), 'Image Title')
	->addColumn('image_description', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		), 'Image Description')
	->addColumn('gallery_img', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		'nullable'  => false,
        ), 'Gallery Image')
	->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Category Id')	
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Enabled');
		
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('imagecategory'))
    ->addColumn('imagecategory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Category ID')
    ->addColumn('category_title', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		'nullable'  => false,
        ), 'Category Title')
	->addColumn('category_img', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		'nullable'  => false,
        ), 'Category Image')
	->addColumn('category_description', Varien_Db_Ddl_Table::TYPE_TEXT, 256, array(
		), 'Category Description');
		
$installer->getConnection()->createTable($table);

$installer->endSetup();