<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'bsslider';
        $this->_controller = 'adminhtml_bsslider';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('bsslider')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_updateButton("delete", "label", Mage::helper("bsslider")->__("Delete Image"));
        $this->_updateButton('save', 'label', Mage::helper('bsslider')->__('Save Image'));
        $this->_formScripts[] = "
            function toggleEditor()
            {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }

            function saveAndContinueEdit()
            {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Make the header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('bsslider_data') && Mage::registry('bsslider_data')->getId()) {
            return Mage::helper('bsslider')->__('Edit Image ') . Mage::helper('bsslider')->__("%s", $this->htmlEscape(Mage::registry('bsslider_data')->getId()));
        } else {
            return Mage::helper('bsslider')->__('New Image');
        }
    }

}
