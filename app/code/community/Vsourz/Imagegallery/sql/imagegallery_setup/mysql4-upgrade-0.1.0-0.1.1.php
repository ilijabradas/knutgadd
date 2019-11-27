<?php
 
$installer = $this;
$installer->startSetup();
 
$resource = Mage::getResourceModel('imagegallery/imagedetail_collection');
if(!method_exists($resource, 'getEntity')){
 
    $table = $this->getTable('imagedetail');
    $query = 'ALTER TABLE `' . $table . '` ADD COLUMN `position` INTEGER NOT NULL DEFAULT 0 AFTER `category_id`';
    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    try {
        $connection->query($query);
    } catch (Exception $e) {
 
    }
}
 
$installer->endSetup();
?>