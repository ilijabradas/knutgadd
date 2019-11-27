<?php	
class Vsourz_Imagegallery_Model_Source_Transition
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'none', 'label' => 'None'),
			array('value' => 'elastic', 'label' => 'Elastic'),
			array('value' => 'fade', 'label' => 'Fade'),
		);
	}
}