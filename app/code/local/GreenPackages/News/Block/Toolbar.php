<?php
class GreenPackages_News_Block_Toolbar extends Mage_Page_Block_Html_Pager
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('news/toolbar.phtml');
    }

    public function setCollection($collection)
    {
        parent::setCollection($collection);
        return $this;
    }


    public function getAvailableLimit()
    {
    	$perPageValues = Mage::getConfig()->getNode('frontend/news/per_page_values');
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        return ($perPageValues);
        return parent::getAvailableLimit();
    }

    public function getLimit()
    {
        $limits = $this->getAvailableLimit();
        if ($limit = $this->getRequest()->getParam($this->getLimitVarName())) {
            if (isset($limits[$limit])) {
                return $limit;
            }
        }
        $defaultLimit = Mage::getStoreConfig('news/frontend/product_per_page');
        if ($defaultLimit != '') {
            return $defaultLimit;
        }
        $limits = array_keys($limits);
        return $limits[0];
    }
}
