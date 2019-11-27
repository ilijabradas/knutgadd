<?php
$categories = array(
	array(
		'category_title' => 'default',
		'status' => '1'
	),
);
foreach ($categories as $category){
	$model = Mage::getModel('imagegallery/imagecategory')->setData($category)->save();
}
