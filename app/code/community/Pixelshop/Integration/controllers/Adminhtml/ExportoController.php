<?php

class Pixelshop_Integration_Adminhtml_ExportoController extends Mage_Adminhtml_Controller_Action
{

	/**
     * View form action
     */
    public function indexAction()
    {
        // $this->_registryObject();
        $this->loadLayout();
        $this->_setActiveMenu('pixelshop/exporto');
        $this->renderLayout();
    }

	/**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function saveAction()
    {
    	if ( Mage::getStoreConfig( 'pixelshop/pixelshop/api_key' ) === '' )
    	{
    		Mage::getModel('core/session')->addError(Mage::helper('pixelshop')->__('Error! Pixelshop API Key missing.'));

    		$this->_redirect('*/*/');
    	}

    	$pixelshop = new Pixelshop_Pixelshop( Mage::getStoreConfig('pixelshop/pixelshop/api_key') );

		$products = Mage::getModel('catalog/product')->getCollection();
		$products->addAttributeToSelect('*');

		$pixelshop_products = array();

		$i = 0;
		foreach( $products as $prod )
		{
			$sync_id = $prod->getId();

			$_product = Mage::getModel('catalog/product')->load($sync_id);

			$pixelshop_products[$i] 	= [
				'sync_id'		=> $sync_id,
				'link'			=> $_product->getProductUrl(),
				'title'			=> $_product->getName(),
				'description'	=> $_product->getShortDescription(),
				'price'			=> $_product->getPrice(),
				'tags'			=> $_product->getMetaKeyword()
			];

			if( $_product->getImage() && $_product->getImage() != 'no_selection' )
				$pixelshop_products[$i]['thumb'] = $_product->getImageUrl();

			$i++;
		}

		$export = $pixelshop->export->products($pixelshop_products);

		if( isset($export['error']) )
		{
			Mage::getModel('core/session')->addError(Mage::helper('pixelshop')->__('Error! Pixelshop API Key incorrect.'));
		}
		else
		{
			Mage::getModel('core/session')->addSuccess(Mage::helper('pixelshop')->__('Success! %s Products success and %s products exists or with error.', $export['success'], $export['error'] + $export['exists']));
		}

    	$this->_redirect('*/*/');
    }
}