<?php

class Nebojsa_Signupwatch_Block_Adminhtml_Signupwatch_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('signupwatchGrid');
		$this->setDefaultSort('signupwatch_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection() {
		$collection = Mage::getModel('signupwatch/signupwatch')->getCollection();
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('signupwatch_id', array(
			'header' => Mage::helper('signupwatch')->__('ID'),
			'align' => 'right',
			'width' => '50px',
			'index' => 'signupwatch_id',
		));

		$this->addColumn('first_name', array(
			'header' => Mage::helper('signupwatch')->__('First name'),
			'align' => 'left',
			'index' => 'first_name',
		));

		$this->addColumn('last_name', array(
			'header' => Mage::helper('signupwatch')->__('Last name'),
			'align' => 'left',
			'index' => 'last_name',
		));

		$this->addColumn('email', array(
			'header' => Mage::helper('signupwatch')->__('Email'),
			'align' => 'left',
			'index' => 'email',
		));

		$this->addColumn('country_code', array(
			'header' => Mage::helper('signupwatch')->__('Country'),
			'align' => 'left',
			'index' => 'country_code',
		));

		$this->addColumn('watch_name', array(
			'header' => Mage::helper('signupwatch')->__('Watch name'),
			'align' => 'left',
			'index' => 'watch_name',
		));
		$this->addColumn('watch_belttype', array(
			'header' => Mage::helper('signupwatch')->__('Watch belt type'),
			'align' => 'left',
			'index' => 'watch_belttype',
		));
		$this->addColumn('watch_date', array(
			'header' => Mage::helper('signupwatch')->__('Watch date'),
			'align' => 'left',
			'index' => 'watch_date',
		));

		$this->addColumn('message', array(
			'header' => Mage::helper('signupwatch')->__('Message'),
			'align' => 'left',
			'index' => 'message',
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('signupwatch')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('signupwatch')->__('XML'));

		return parent::_prepareColumns();
	}

}
