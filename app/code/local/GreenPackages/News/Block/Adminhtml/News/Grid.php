<?php

class GreenPackages_News_Block_Adminhtml_News_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('newsGrid');
    $this->setDefaultSort('news_id');
    $this->setDefaultDir('ASC');
    $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
   $collection = Mage::getModel('news/news')->getCollection();
   $this->setCollection($collection);

   return parent::_prepareCollection();
 }

 protected function _prepareColumns()
 {
  $this->addColumn('news_id', array(
    'header'    => Mage::helper('news')->__('ID'),
    'align'     =>'right',
    'width'     => '50px',
    'index'     => 'news_id',
    ));

  $this->addColumn('title', array(
    'header'    => Mage::helper('news')->__('Title'),
    'align'     =>'left',
    'index'     => 'title',
    ));

  $this->addColumn('texto', array(
    'header'    => Mage::helper('news')->__('Content'),
    'align'     =>'left',
    'index'     => 'texto',
    'renderer'  => 'adminhtml/widget_grid_column_renderer_longtext',
    'string_limit'  => '100',
    ));

  $this->addColumn('image_one', array(
    'header'    => Mage::helper('news')->__('Left Image'),
    'align'     =>'left',
    'index'     => 'image_one',
    'renderer'  => 'GreenPackages_News_Block_Adminhtml_News_Renderer_Imageone',
    ));

  $this->addColumn('image_two', array(
    'header'    => Mage::helper('news')->__('Right Image'),
    'align'     =>'left',
    'index'     => 'image_two',
    'renderer'  => 'GreenPackages_News_Block_Adminhtml_News_Renderer_Imagetwo',
    ));

  $this->addColumn('status', array(
    'header'    => Mage::helper('news')->__('Status'),
    'align'     => 'left',
    'width'     => '80px',
    'index'     => 'status',
    'type'      => 'options',
    'options'   => array(
      1 => 'Enabled',
      2 => 'Disabled',
      ),
    ));
  $this->addColumn('action',
    array(
      'header'    =>  Mage::helper('news')->__('Action'),
      'width'     => '100',
      'type'      => 'action',
      'getter'    => 'getId',
      'actions'   => array(
        array(
          'caption'   => Mage::helper('news')->__('Edit'),
          'url'       => array('base'=> '*/*/edit'),
          'field'     => 'id'
          )
        ),
      'filter'    => false,
      'sortable'  => false,
      'index'     => 'stores',
      'is_system' => true,
      ));

  $this->addExportType('*/*/exportCsv', Mage::helper('news')->__('CSV'));
  $this->addExportType('*/*/exportXml', Mage::helper('news')->__('XML'));

  return parent::_prepareColumns();
}

protected function _prepareMassaction()
{
  $this->setMassactionIdField('news_id');
  $this->getMassactionBlock()->setFormFieldName('news');

  $this->getMassactionBlock()->addItem('delete', array(
   'label'    => Mage::helper('news')->__('Delete'),
   'url'      => $this->getUrl('*/*/massDelete'),
   'confirm'  => Mage::helper('news')->__('Are you sure?')
   ));

  $statuses = Mage::getSingleton('news/status')->getOptionArray();

  array_unshift($statuses, array('label'=>'', 'value'=>''));
  $this->getMassactionBlock()->addItem('status', array(
   'label'=> Mage::helper('news')->__('Change status'),
   'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
   'additional' => array(
    'visibility' => array(
     'name' => 'status',
     'type' => 'select',
     'class' => 'required-entry',
     'label' => Mage::helper('news')->__('Status'),
     'values' => $statuses
     )
    )
   ));
  return $this;
}



}
