<?php
class Wsu_Networksecurities_Adminhtml_FailedloginController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
        $this->_title($this->__('networksecurities'))->_title($this->__('failed logins'));
        $this->loadLayout();
        $this->_setActiveMenu('tools/failedlogin');
        $this->_addContent($this->getLayout()->createBlock('wsu_networksecurities/adminhtml_failedlogin'));
        $this->renderLayout();
	}
    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('wsu_networksecurities/adminhtml_failedlogin_grid')->toHtml()
        );
    }
	
	
}
