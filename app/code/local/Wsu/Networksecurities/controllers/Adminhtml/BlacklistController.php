<?php
class Wsu_Networksecurities_Adminhtml_BlacklistController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('tools/blacklist')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Blacklist'));
		return $this;
	}
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}
}
