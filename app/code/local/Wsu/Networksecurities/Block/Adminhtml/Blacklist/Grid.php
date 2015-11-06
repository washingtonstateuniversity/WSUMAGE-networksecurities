<?php
class Wsu_Networksecurities_Block_Adminhtml_Blacklist_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
        parent::__construct();
        $this->setId('blacklist_grid');
        $this->setDefaultSort('blacklist_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
	}
	protected function _prepareCollection() {
		$collection = Mage::getModel('wsu_networksecurities/blacklist')->getCollection();
        $this->setCollection($collection);
        return $this;
	}
	protected function _prepareColumns() {  
		$this->addColumn('blacklist_id', array(
		  'header'    => Mage::helper('wsu_networksecurities')->__('ID'),
		  'align'     => 'left',
		  'index'     => 'blacklist_id',
		));
		$this->addColumn('ip', array(
		  'header'    => Mage::helper('wsu_networksecurities')->__('IP'),
		  'align'     => 'left',
		  'index'     => 'ip',
		));
		$this->addColumn('log_at', array(
		  'header'		=> Mage::helper('wsu_networksecurities')->__('Failed Date'),
		  'align'		=> 'left',
		  'type'		=> 'datetime',
		  'index'		=> 'log_at',
		));
		
		$link = Mage::helper('adminhtml')->getUrl('adminhtml/blacklist/remove/') .'id/$blacklist_id';
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
	protected function _prepareMassaction(){
        $this->setMassactionIdField('blacklist_id');
        $this->getMassactionBlock()->setFormFieldName('blacklist_ids');
		
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('wsu_networksecurities')->__('Delete'),
             'url'      => $this->getUrl('*/*/massRemove'),
             'confirm'  => Mage::helper('wsu_networksecurities')->__('Are you sure?')
        ));
        return $this;
    }
	
}
