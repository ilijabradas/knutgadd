<?php

class GreenPackages_News_Block_Adminhtml_News_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset('news_form', array('legend'=>Mage::helper('news')->__('News information')));
    $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false, 'add_widgets' => false,'files_browser_window_url'=>$this->getBaseUrl().'admin/cms_wysiwyg_images/index/'));

    $fieldset->addField('title', 'text', array(
      'label'     => Mage::helper('news')->__('Title'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'title',
      ));

    $fieldset->addField('image_one', 'image', array(
      'label'     => Mage::helper('news')->__('Left Image'),
      'required'  => false,
      'name'      => 'image_one',
      ));

    $fieldset->addField('image_two', 'image', array(
      'label'     => Mage::helper('news')->__('Right Image'),
      'required'  => false,
      'name'      => 'image_two',
      ));

    $currentDate = date('d M Y');

    $fieldset->addField('created_at', 'hidden', array(
      'name'      => 'created_at',
      'value'     => $currentDate,
      ));

    $fieldset->addField('status', 'select', array(
      'label'     => Mage::helper('news')->__('Status'),
      'name'      => 'status',
      'values'    => array(
        array(
          'value'     => 1,
          'label'     => Mage::helper('news')->__('Enabled'),
          ),

        array(
          'value'     => 2,
          'label'     => Mage::helper('news')->__('Disabled'),
          ),
        ),
      ));

    $fieldset->addField('texto', 'editor', array(
      'name'      => 'texto',
      'label'     => Mage::helper('news')->__('Content'),
      'title'     => Mage::helper('news')->__('Content'),
      'style'     => 'width:700px; height:500px;',


      'required'  => true,
      'config'    => $wysiwygConfig
      ));
    
    if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
    {
      $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
      Mage::getSingleton('adminhtml/session')->setNewsData(null);
    } elseif ( Mage::registry('news_data') ) {
      $form->setValues(Mage::registry('news_data')->getData());
    }

    return parent::_prepareForm();
  }
}
