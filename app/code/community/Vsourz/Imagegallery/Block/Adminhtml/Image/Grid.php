<?php
class Vsourz_Imagegallery_Block_Adminhtml_Image_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	public function __construct(){
		parent::__construct();
		$this->setId('image_grid');
		$this->setDefaultSort('imagedetail_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	protected function _prepareCollection(){
		$collection = Mage::getModel('imagegallery/imagedetail')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns(){
		 $this->addColumn('imagedetail_id', array(
			'header' => Mage::helper('imagegallery')->__('ID'),
			'align' => 'right',
			'width' => '10px',
			'index' => 'imagedetail_id',
		));
		$this->addColumn('image_title', array(
			'header' => Mage::helper('imagegallery')->__('Image Title'),
			'align' => 'right',
			'width' => '100px',
			'index' => 'image_title',
		));
		$this->addColumn('gallery_img', array(
			'header' => Mage::helper('imagegallery')->__('Image'),
			'align' => 'left',
			'width' => '200px',
			'index' => 'gallery_img',
			'renderer' => 'imagegallery/adminhtml_imagegallery_renderer_image',
		));
		$this->addColumn('image_description', array(
			'header' => Mage::helper('imagegallery')->__('Image Description'),
			'align' => 'right',
			'width' => '200px',
			'index' => 'image_description',
		));
		$this->addColumn('category_id', array(
			'header' => Mage::helper('imagegallery')->__('Image Category'),
			'align' => 'right',
			'width' => '50px',
			'index' => 'category_id',
			'renderer' => 'imagegallery/adminhtml_imagegallery_renderer_category'
		));		
		$this->addColumn('position', array(
			'header' => Mage::helper('imagegallery')->__('Position'),
			'align' => 'right',
			'width' => '100px',
			'index' => 'position',
		));
		$this->addColumn('status', array(
			'header' => Mage::helper('imagegallery')->__('Status'),
			'align' => 'left',
			'width' => '50px',
			'index' => 'status',
			'renderer' => 'imagegallery/adminhtml_imagegallery_renderer_status',
		));
		
	return parent::_prepareColumns();
	}
	protected function _prepareMassaction(){
		$this->setMassactionIdField('imagedetail_id');
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