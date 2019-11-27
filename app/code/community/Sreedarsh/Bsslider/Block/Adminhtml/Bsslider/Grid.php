<?php

/**
 * Sreedarsh Bsslider
 *
 * @category      Sreedarsh
 * @package       Sreedarsh_Bsslider
 */

class Sreedarsh_Bsslider_Block_Adminhtml_Bsslider_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct($arguments = array())
    {
        parent::__construct($arguments);
        $this->setId('bssliderGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bsslider/bsslider')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header' => Mage::helper('bsslider')->__('ID'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'id',
        ));

        $this->addColumn('image_title', array(
            'header' => Mage::helper('bsslider')->__('Image Title'),
            'align' => 'left',
            'width' => '20px',
            'index' => 'image_title',
        ));
        $this->addColumn('image_caption', array(
            'header' => Mage::helper('bsslider')->__('Image Caption'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'image_caption',
        ));
        $this->addColumn('image_link', array(
            'header' => Mage::helper('bsslider')->__('Image Link'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'image_link',
        ));


        $this->addColumn('status', array(
            'header' => Mage::helper('bsslider')->__('Status'),
            'width' => '50px',
            'type' => 'options',
            'options' => array(
                Mage::helper('bsslider')->__('Inactive'),
                Mage::helper('bsslider')->__('Active')
            ),
            'renderer' => new Sreedarsh_Bsslider_Block_Adminhtml_Renderer_Status(),
            'index' => 'status'
        ));


        $this->addColumn('image_name', array(
            'header' => Mage::helper('bsslider')->__('Image'),
            'align' => 'left',
            'width' => '10px',
            'filter' => false,
            'sortable' => false,
            'index' => 'image_name',
            'renderer' => new Sreedarsh_Bsslider_Block_Adminhtml_Renderer_Image(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('bsslider')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('bsslider')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
                )
        );
    }

    /**
     * Prepare grid massaction actions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('bsslider');
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('bsslider')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('bsslider')->__('Are you sure to delete?')
        ));
        return $this;
    }

    /**
     * Grid url getter
     *
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Grid row getter
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
