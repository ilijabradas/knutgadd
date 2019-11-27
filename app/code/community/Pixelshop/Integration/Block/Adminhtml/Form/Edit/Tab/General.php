<?php

class Pixelshop_Integration_Block_Adminhtml_Form_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * prepare form in tab
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('pixelshop');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('general_');
        $form->setFieldNameSuffix('general');

        // $fieldset = $form->addFieldset('display', array(
        //     'legend'       => $helper->__('Display Settings'),
        //     'class'        => 'fieldset-wide'
        // ));

        // $fieldset->addField('label', 'text', array(
        //     'name'         => 'label',
        //     'label'        => $helper->__('Label'),
        // ));

        $fieldset = $form->addField('submit', 'submit', array(
            'value'  => 'Export Products',
            'after_element_html' => '<small></small>',
            'class' => 'form-button',
            'tabindex' => 1
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }

}