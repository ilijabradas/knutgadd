<?php
class Nebojsa_Signupwatch_Block_Adminhtml_Signupwatch extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_signupwatch';
    $this->_blockGroup = 'signupwatch';
    $this->_headerText = Mage::helper('signupwatch')->__('Signupwatch Manager');
    parent::__construct();
  }
}
