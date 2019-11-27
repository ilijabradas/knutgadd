<?php
class Vsourz_Imagegallery_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	public function __construct(){
		parent::__construct();
		$this->setId('category_grid');
		$this->setDefaultSort('imagecategory_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	protected function _prepareCollection(){
		$collection = Mage::getModel('imagegallery/imagecategory')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns(){
		 $this->addColumn('imagecategory_id', array(
			'header' => Mage::helper('imagegallery')->__('ID'),
			'align' => 'right',
			'width' => '10px',
			'index' => 'imagecategory_id',
		));
		$this->addColumn('category_title', array(
			'header' => Mage::helper('imagegallery')->__('Category Title'),
			'align' => 'right',
			'width' => '100px',
			'index' => 'category_title',
		));
		$this->addColumn('category_img', array(
			'header' => Mage::helper('imagegallery')->__('Category Image'),
			'align' => 'left',
			'width' => '200px',
			'index' => 'category_img',
			'renderer' => 'imagegallery/adminhtml_imagegallery_renderer_catimage',
		));
		$this->addColumn('category_description', array(
			'header' => Mage::helper('imagegallery')->__('Category Description'),
			'align' => 'right',
			'width' => '250px',
			'index' => 'category_description',
		));		
	return parent::_prepareColumns();
	}
	protected function _prepareMassaction(){
		$this->setMassactionIdField('imagecategory_id');
		$this->getMassactionBlock()->setFormFieldName('id');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('imagegallery')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
			'confirm' => Mage::helper('imagegallery')->__('Are you sure?')
		));
		return $this;
	}
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
}