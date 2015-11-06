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
	public function removeAction($id=0) {
		$requestId = $this->getRequest()->getParam('id');
		$failedlogin_id = ($id > 0) ? $id : $requestId;
		$affected = array();
		$starting = 1;
		if( $failedlogin_id > 0 ) {
				$model = Mage::getModel('wsu_networksecurities/failedlogin');
				$model->load($failedlogin_id);			
				$ip = $model->getIp();
			try {
				$model->delete();
				if($requestId>0){
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('wsu_networksecurities')->__('Removed '.$ip.' from the failedlogin listing')
					);
				}
				$data = array( 'ip' => $ip );
				Mage::dispatchEvent('failedlogin_removed', $data);
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/');
			}
		}else{
			Mage::getSingleton('adminhtml/session')->addError("failed to get key");
			$this->_redirect('*/*/');
		}
	}	
}
