<?php
$cmsBlocks = array(
	array(
		'title'         => 'Static Block',
		'identifier'    => 'static-block',
		'content'       => 'PHP Script to create or update static blocks',
		'is_active'     => 1,
		'stores'        => 1
	),
	array(
		'title'         => 'Static Block 02',
		'identifier'    => 'static-block-02',
		'content'       => 'PHP Script to create or update static blocks-Content Edit',
		'is_active'     => 1,
		'stores'        => 1
	)
);
/**
 * Insert default blocks
 */
foreach ($cmsBlocks as $data) {
	// Check if static block already exists:
	$collection = Mage::getModel('cms/block')->load($data['identifier']);
	$block_identifier = $collection->getData('identifier');
	if(!$block_identifier)
	{
		// Create static block:
		Mage::getModel('cms/block')->setData($data)->save();
	}else{
		//Update Content if static block already exists
		Mage::getModel('cms/block')->load($data['identifier'])
		    ->setData('content', $data['content'])
		    ->save();
	}
}
