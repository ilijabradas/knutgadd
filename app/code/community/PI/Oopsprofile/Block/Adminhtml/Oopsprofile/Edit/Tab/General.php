<?php
class PI_Oopsprofile_Block_Adminhtml_Oopsprofile_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Container
{

    protected $_storeModel;
    protected $_attributes;
    protected $_addMapButtonHtml;
    protected $_removeMapButtonHtml;
    protected $_shortDateFormat;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('oopsprofile/general.phtml');
    }

    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadCalendarJs(true);
        }
        return $this;
    }

    public function getOopsSelected($key, $value)
    {
        return $this->getOopsData($key)==$value ? 'selected="selected"' : '';
    }

    public function getOopsData($key)
    {
    		$oopsData = Mage::registry('current_convert_oopsprofile');
    		return $oopsData[$key];
    }

    public function getAttributes($entityType)
    {
        if (!isset($this->_attributes[$entityType])) {
            switch ($entityType) {
                case 'product':
                    $attributes = Mage::getSingleton('catalog/convert_parser_product')
                        ->getExternalAttributes();
                    break;

                case 'customer':
                    $attributes = Mage::getSingleton('customer/convert_parser_customer')
                        ->getExternalAttributes();
                    break;
                case 'order':
                    $attributes = Mage::helper('oopsprofile/table')->getOrderFields();
                    break;
                case 'invoice':
                    $attributes = Mage::helper('oopsprofile/table')->getInvoiceFields();
                    break;
                case 'shipment':
                    $attributes = Mage::helper('oopsprofile/table')->getShipmentFields();
                    break;
                case 'creditmemo':
                    $attributes = Mage::helper('oopsprofile/table')->getCreditmemoFields();
                    break;
            }

            array_splice($attributes, 0, 0, array(''=>$this->__('Choose an attribute')));
            $this->_attributes[$entityType] = $attributes;
        }
        return $this->_attributes[$entityType];
    }

    public function getValue($key, $default='', $defaultNew = null)
    {
        if (null !== $defaultNew) {
            if (0 == $this->getProfileId()) {
                $default = $defaultNew;
            }
        }

        $value = $this->getData($key);
        return $this->escapeHtml(strlen($value) > 0 ? $value : $default);
    }

    public function getSelected($key, $value)
    {
        return $this->getData($key)==$value ? 'selected="selected"' : '';
    }

    public function getChecked($key)
    {
        return $this->getData($key) ? 'checked="checked"' : '';
    }

    public function getMappings($entityType)
    {
        $maps = $this->getData('gui_data/map/'.$entityType.'/db');
        return $maps ? $maps : array();
    }

    public function getAddMapButtonHtml()
    {
        if (!$this->_addMapButtonHtml) {
            $this->_addMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('add')->setLabel($this->__('Add Field Mapping'))
                ->setOnClick("addFieldMapping()")->toHtml();
        }
        return $this->_addMapButtonHtml;
    }

    public function getRemoveMapButtonHtml()
    {
        if (!$this->_removeMapButtonHtml) {
            $this->_removeMapButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
                ->setClass('delete')->setLabel($this->__('Remove'))
                ->setOnClick("removeFieldMapping(this)")->toHtml();
        }
        return $this->_removeMapButtonHtml;
    }

    public function getProductTypeFilterOptions()
    {
        $options = Mage::getSingleton('catalog/product_type')->getOptionArray();
        array_splice($options, 0, 0, array(''=>$this->__('Any Type')));
        return $options;
    }

    public function getProductAttributeSetFilterOptions()
    {
        $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $opt = array();
        $opt = array(''=>$this->__('Any Attribute Set'));
        if ($options) foreach($options as $index => $value) {
            $opt[$index]  = $value;
        }
        //array_slice($options, 0, 0, array(''=>$this->__('Any Attribute Set')));
        return $opt;
    }

    public function getProductVisibilityFilterOptions()
    {
        $options = Mage::getSingleton('catalog/product_visibility')->getOptionArray();

        array_splice($options, 0, 0, array(''=>$this->__('Any Visibility')));
        return $options;
    }

    public function getProductStatusFilterOptions()
    {
        $options = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_splice($options, 0, 0, array(''=>$this->__('Any Status')));
        return $options;
    }

    public function getStoreFilterOptions()
    {
        if (!$this->_filterStores) {
            #$this->_filterStores = array(''=>$this->__('Any Store'));
            $this->_filterStores = array();
            foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
                if ($storeNode->getName()==='default') {
                    //continue;
                }
                $this->_filterStores[$storeNode->getName()] = (string)$storeNode->system->store->name;
            }
        }
        return $this->_filterStores;
    }

    public function getCustomerGroupFilterOptions()
    {
        $options = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=>0))
            ->load()
            ->toOptionHash();

        array_splice($options, 0, 0, array(''=>$this->__('Any Group')));
        return $options;
    }

    public function getCountryFilterOptions()
    {
        $options = Mage::getResourceModel('directory/country_collection')
            ->load()->toOptionArray(false);
        array_unshift($options, array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('All countries')));
        return $options;
    }

    /**
     * Retrieve system store model
     *
     * @return Mage_Adminhtml_Model_System_Store
     */
    protected function _getStoreModel() {
        if (is_null($this->_storeModel)) {
            $this->_storeModel = Mage::getSingleton('adminhtml/system_store');
        }
        return $this->_storeModel;
    }

    public function getWebsiteCollection()
    {
        return $this->_getStoreModel()->getWebsiteCollection();
    }

    public function getGroupCollection()
    {
        return $this->_getStoreModel()->getGroupCollection();
    }

    public function getStoreCollection()
    {
        return $this->_getStoreModel()->getStoreCollection();
    }

    public function getShortDateFormat()
    {
        if (!$this->_shortDateFormat) {
            $this->_shortDateFormat = Mage::app()->getLocale()->getDateStrFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
            );
        }
        return $this->_shortDateFormat;
    }

}

