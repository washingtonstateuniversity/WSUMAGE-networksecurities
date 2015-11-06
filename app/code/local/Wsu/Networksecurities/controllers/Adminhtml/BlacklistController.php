<?php
class Wsu_Networksecurities_Adminhtml_BlacklistController extends Mage_Adminhtml_Controller_Action {
    /**
     * Inits the layout, the active menu tab and the breadcrumbs
     *
     * @return Wsu_Auditing_Adminhtml_HistoryController
     */
    protected function _initAction() {
        $this->loadLayout();
        $this->_setActiveMenu('auditing/blacklist');
        $this->_addBreadcrumb(
            Mage::helper('wsu_networksecurities')->__('Admin Monitoring'),
            Mage::helper('wsu_networksecurities')->__('Blacklist')
        );

        return $this;
    }
	public function indexAction() {
        $this->_title($this->__('networksecurities'))->_title($this->__('Blacklist'));
        $this->loadLayout();
        $this->_setActiveMenu('tools/blacklist');
        $this->_addContent($this->getLayout()->createBlock('wsu_networksecurities/adminhtml_blacklist'));
        $this->renderLayout();
	}
    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('wsu_networksecurities/adminhtml_blacklist_grid')->toHtml()
        );
    }
	
	
	
	public function removeAction($id=0) {
		$requestId = $this->getRequest()->getParam('id');
		$blacklist_id = ($id > 0) ? $id : $requestId;
		$affected = array();
		$starting = 1;
		if( $blacklist_id > 0 ) {
				$model = Mage::getModel('wsu_networksecurities/blacklist');
				$model->load($blacklist_id);			
				$ip = $model->getIp();
			try {
				$model->delete();
				if($requestId>0){
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('wsu_networksecurities')->__('Removed '.$ip.' from the blacklisting')
					);
				}
				$data = array( 'ip' => $ip );
				Mage::dispatchEvent('blacklist_removed', $data);
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
    public function massRemoveAction() {
        $blacklist_ids = $this->getRequest()->getParam('blacklist_ids');
        if(!is_array($blacklist_ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($blacklist_ids as $id) {
					$this->removeAction($id);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully removed', count($id)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	
	
}
