<?php
class Wsu_NetworkSecurities_Adminhtml_FailedlogintrackerController extends Mage_Adminhtml_Controller_Action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('customer/failedlogintracker')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}
}
