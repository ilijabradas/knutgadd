<?php

class GreenPackages_News_Block_Links extends Mage_Page_Block_Template_Links
{
   
    public function addNewsLink()
    {
        		
		$text = $this->__('Noticias');
		
		$this->addLink($text, 'news/listado', $text, true, array(), 30, null, 'class="top-link-wishlist"');
		
        return $this;
    }
}
