<?php

class Sreedarsh_Bsslider_Block_Adminhtml_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param  Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $data = $row->getData($this->getColumn()->getIndex());
        $status = ($data ? $this->helper('bsslider')->__('Active') : $this->helper('bsslider')->__('Inactive'));

        return $status;
    }

}
