<?php

class Webinerds_Catalog_Block_Product_List_Related extends Mage_Catalog_Block_Product_List_Related {

	protected function _prepareData()
	{
		$product = Mage::registry('product');

		$this->_itemCollection = $product->getRelatedProductCollection()
		                                 ->addAttributeToSelect('required_options')
																		 ->addAttributeToSelect('belt_type')
		                                 ->setPositionOrder()
		                                 ->addStoreFilter()
		;

		if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
			Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
				Mage::getSingleton('checkout/session')->getQuoteId()
			);
			$this->_addProductAttributesAndPrices($this->_itemCollection);
		}

		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

		$this->_itemCollection->load();

		foreach ($this->_itemCollection as $product) {
			$product->setDoNotUseCategoryId(true);
		}

		return $this;
	}
}