<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

//Adding Static Blocks

// Data:
//$data = array(
//    'title'         => 'Title',
//    'identifier'    => 'identifier',
//    'stores'        => array(0), // Array with store ID's
//    'content'       => '<p>Some HTML content for example</p>',
//    'is_active'     => 1
//);
//
//// Check if static block already exists:
//$block = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($data['identifier']);
//if($block->getId() == false)
//{
//    // Create static block:
//    Mage::getModel('cms/block')->setData($data)->save();
//}


//Add custom product attribute

$installer->addAttribute('catalog_product', 'belt_type', array(
    'group'             => 'General',
    'label'             => 'Belt Type',
    'type'              => 'text',
    'input'             => 'text',
    'frontend'          => '',
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => true,
    'visible_in_advanced_search' => false,
    'unique'            => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->endSetup();