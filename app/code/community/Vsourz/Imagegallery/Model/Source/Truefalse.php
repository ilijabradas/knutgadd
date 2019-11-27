<?php	
class Vsourz_Imagegallery_Model_Source_Truefalse
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'true', 'label' => 'True'),
			array('value' => 'false', 'label' => 'False'),
		);
	}
}
