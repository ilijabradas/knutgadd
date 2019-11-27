<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Block_Adminhtml_Bsslider_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Sreedarsh_Bsslider_Block_Adminhtml_Bsslider_Edit_Form
     */
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');

        $this->setImageId($id);
        $data = $this->_prepareCollection();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $id)),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        $fieldset = $form->addFieldset('edit_form_fieldset', array(
            'legend' => Mage::helper('bsslider')->__('Slider image')
        ));

        $fieldset->addField('image_name', 'image', array(
            'label' => Mage::helper('bsslider')->__('Image'),
            'class' => 'entry',
            'required' => true,
            'name' => 'image_name',
            'note' => '(*.jpg, *.png, *.gif)'
        ));

        $fieldset->addField('image_title', 'text', array(
            'label' => Mage::helper('bsslider')->__('Image Title'),
            'class' => 'entry',
            'required' => false,
            'name' => 'image_title',
        ));

        $fieldset->addField('image_caption', 'textarea', array(
            'label' => Mage::helper('bsslider')->__('Image Caption'),
            'class' => 'entry',
            'required' => false,
            'name' => 'image_caption',
        ));

        $fieldset->addField('image_link', 'text', array(
            'label' => Mage::helper('bsslider')->__('Image Link'),
            'class' => 'entry',
            'required' => false,
            'name' => 'image_link',
        ));

        $fieldset->addField('status', 'select', array(
            'name' => 'status',
            'label' => Mage::helper('bsslider')->__('Status'),
            'title' => Mage::helper('bsslider')->__('Status'),
            'required' => true,
            'values' => array(0 => 'Inactive', 1 => 'Active'),
        ));

        
        $form->setValues($data);

        return parent::_prepareForm();
    }

    /**
     * Prepare form collection object
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $model = Mage::getModel('bsslider/bsslider');
        $collection = $model->getCollection()
                ->getSliderdetails($this->getImageId());
        $data = $collection->getData();

        return $data[0];
    }

}
