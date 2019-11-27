<?php

class GreenPackages_News_Model_Mysql4_News_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('news/news');
    }

public function prepareSummary()
{
		$this->setConnection($this->getResource()->getReadConnection());
				
		$this->getSelect()
			->from(array('main_table'=>'news'),'*');
		
		return $this;
}

public function getDetalle($news_id)
{
	
		$this->setConnection($this->getResource()->getReadConnection());
		$this->getSelect()
			->from(array('main_table'=>'news'),'*')
			->where('news_id = ?', $news_id);


		return $this;


}


}
