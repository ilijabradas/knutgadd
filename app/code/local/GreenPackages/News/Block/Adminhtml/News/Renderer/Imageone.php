<?php
class GreenPackages_News_Block_Adminhtml_News_Renderer_Imageone extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
	 	$image = $row->getImageOne();
		
		return '<img src="'.$image.'" height="80" />';
	}
}
?>
