<?php

class GreenPackages_News_ListadoController extends Mage_Core_Controller_Front_Action
{
	/*showing list of those news which are active---------*/
	public function indexAction()
	{
		
		$this->loadLayout();  
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb('home',array('label'=>Mage::helper('cms')->__('Home'),                  					'title'=>Mage::helper('cms')->__('Home Page'),
           			'link'=>Mage::getBaseUrl())); 
		$breadcrumbs->addCrumb('News & Innovation', array('label'=>$this->__('News & Innovation'), 
                 		'title'=>'News & Innovation'
                 		));
 
		
 
		//echo $this->getLayout()->getBlock('breadcrumbs')->toHtml();   
		$this->renderLayout();
	}
}
