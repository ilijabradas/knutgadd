<?php

class Pixelshop_Integration_Block_Adminhtml_Form_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'pixelshop';
        $this->_controller = 'adminhtml_form';
        $this->_headerText = Mage::helper('pixelshop')->__('Export Products');
    }

}