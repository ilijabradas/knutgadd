<?php

class GreenPackages_News_Block_Listado extends Mage_Core_Block_Template{


	public function _construct(){
		
		}
	
	protected $_newsCollection = null;

	
	/**
     * Recogemos la collection de news
     *
     */
	public function getLoadedNewsCollection()
	{
		return $this->_getNewsCollection();
	}

	
	/** Devuelve el listado de noticias de news
     *
     */
	public function _getNewsCollection()
	{
			if (is_null($this->_newsCollection)) {
				//$now = date('Y-m-d');
				$now = now();
				
				//$this->_newsCollection = Mage::getModel('news/news')->getCollection()->prepareSummary();
				$this->_newsCollection = Mage::getModel('news/news')->getCollection();
			
				$this->_newsCollection->getSelect('*');
				$this->_newsCollection->addFieldToFilter('status', '1')->addFieldToFilter('dateto',array('gteq'=>$now))
					->setOrder('datefrom','desc');

			}

		return $this->_newsCollection;
	}

	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
	public function _beforeToHtml()
	{
		$toolbar = $this->getLayout()->createBlock('news/toolbar', microtime());

		$toolbar->setCollection($this->_getNewsCollection());
		$this->setChild('toolbar', $toolbar);
		Mage::dispatchEvent('news_block_listado_collection', array(
		'collection'=>$this->_getNewsCollection(),
		));

		$this->_getNewsCollection()->load();
		return parent::_prepareLayout();
	}

	/**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
	public function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}
}
