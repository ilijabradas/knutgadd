<?php
class PI_Oopsprofile_Block_Adminhtml_System_Convert_Gui_Grid extends Mage_Adminhtml_Block_System_Convert_Gui_Grid
{

		protected function _widgetprepareCollection()
    {
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

		protected function _prepareCollection()
    {
    
				$resource = Mage::getSingleton('core/resource');
				$oopsdataflowTable = $resource->getTableName('oopsprofile/oopsprofile');

				$oopsProfileIds = Mage::getResourceModel('oopsprofile/oopsprofile_collection')->getData();
				$ids = array();
				foreach($oopsProfileIds as $oopsProfileId)
				{
						$ids[] = $oopsProfileId['dataflow_profile_id'];
				}
				//print_r($ids);exit;
    
        $collection = Mage::getResourceModel('dataflow/profile_collection')
            ->addFieldToFilter('entity_type', array('notnull'=>''))
            ->addFieldToFilter('profile_id', array('nin'=>$ids));

        $this->setCollection($collection);

        return $this->_widgetprepareCollection();
    }
}
