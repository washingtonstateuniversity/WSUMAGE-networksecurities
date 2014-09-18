<?php
class Wsu_Networksecurities_Block_Adminhtml_Failedlogin_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
	parent::__construct();
		$this->setId('failedlogin_grid');
		$this->setDefaultSort('failedlogin_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel('wsu_networksecurities/failedlogin')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns() {  
		$this->addColumn('failedlogin_id', array(
			'header'    => Mage::helper('wsu_networksecurities')->__('Login ID'),
			'align'     =>'left',
			'index'     => 'failedlogin_id',
		));
		$this->addColumn('ip', array(
		  'header'    => Mage::helper('wsu_networksecurities')->__('IP'),
		  'align'     => 'left',
		  'index'     => 'ip',
		));
		$this->addColumn('login', array(
		  'header'    => Mage::helper('wsu_networksecurities')->__('Login'),
		  'align'     => 'left',
		  'index'     => 'login',
		));
		$this->addColumn('created_at', array(
			'header'    => Mage::helper('wsu_networksecurities')->__('Failed On'),
			'align'     =>'left',
			'type' => 'datetime',
			'index'     => 'created_at',
		));
		$link = Mage::helper('adminhtml')->getUrl('networksecurities/adminhtml_failedlogin/remove/') .'id/$failedlogin_id';
		$this->addColumn('action_delete',array(
			'header'	=> Mage::helper('wsu_networksecurities')->__('Actions'),
			'type'		=> 'action',
			'actions'	=> array(
				array(
					'caption' => Mage::helper('wsu_networksecurities')->__('Delete'),
					'url'     => $link,
					'confirm' => Mage::helper('wsu_networksecurities')->__('Are you sure?')
				),
			),
			'filter'    => false,
			'sortable'  => false,
		));
		return parent::_prepareColumns();
	}
}
