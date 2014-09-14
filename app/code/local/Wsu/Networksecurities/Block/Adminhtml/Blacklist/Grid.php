<?php
class Wsu_Networksecurities_Block_Adminhtml_Networksecurities_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
	parent::__construct();
		$this->setId('Blacklist');
		$this->setDefaultSort('created_at');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel('wsu_networksecurities/blacklist')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns() {  
		$this->addColumn('blacklist_id', array(
		  'header'    => Mage::helper('wsu_networksecurities')->__('ID'),
		  'align'     =>'left',
		  'index'     => 'blacklist_id',
		));
		$this->addColumn('ip', array(
		  'header'    => Mage::helper('wsu_networksecurities')->__('IP'),
		  'align'     =>'left',
		  'index'     => 'ip',
		));
		$this->addColumn('log_at', array(
		  'header'    => Mage::helper('wsu_networksecurities')->__('Failed Date'),
		  'align'     =>'left',
		  'type' => 'datetime',
		  'index'     => 'log_at',
		));
		$this->addColumn('action',array(
			'header'    => Mage::helper('wsu_networksecurities')->__('Actions'),
			'type'      => 'action',
			'getter'     => 'getId',
			'actions'   => array(
				array(
					'caption' => Mage::helper('wsu_networksecurities')->__('Delete'),
					'url'     => array('base'=>'*/*/remove'),
					'confirm' => true
				),
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'blacklist_id',
			'is_system' => true,
		));
		return parent::_prepareColumns();
	}
	protected function _prepareMassaction(){
        $this->setMassactionIdField('blacklist_id');
        $this->getMassactionBlock()->setFormFieldName('blacklist_ids');
		
        $this->getMassactionBlock()->addItem('resort_blacklist', array(
             'label'    => Mage::helper('wsu_networksecurities')->__('Delete'),
             'url'      => $this->getUrl('*/*/massRemove'),
             'confirm'  => Mage::helper('wsu_networksecurities')->__('Are you sure?')
        ));
        return $this;
    }
	
}
