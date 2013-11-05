<?php
class Wsu_NewtworkSecurities_Adminhtml_WsuController extends Mage_Core_Controller_Front_Action{

	/**
	 * Forgot administrator password action
	 * Request Access 
	 */
	public function requestaccessAction() {
		Mage::registry('isSecureArea'); // acting is if we are in the admin
		Mage::app('admin')->setUseSessionInUrl(false);
		Mage::getSingleton('core/session', array('name' => 'adminhtml'));

		$email    = (string) $this->getRequest()->getParam('email');
		$store_id = (string) $this->getRequest()->getParam('store');
		$params   = $this->getRequest()->getParams();
		if (!empty($email) && !empty($params)) {
			// Validate received data to be an email address
			if (Zend_Validate::is($email, 'EmailAddress')) {
				$collection = Mage::getResourceModel('admin/user_collection');
				/** @var $collection Mage_Admin_Model_Resource_User_Collection */
				$collection->addFieldToFilter('email', $email);
				$collection->load(false);
				$found = false;
				if ($collection->getSize() > 0) {
					foreach ($collection as $item) {
						$user = Mage::getModel('admin/user')->load($item->getId());
						if ($user->getId()) {
							$found = true;
						}
						break;
					}
				}
				if ($found) {
					//email the user	
					$this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('A request for this email, %s, to be added to the admin users.', Mage::helper('adminhtml')->escapeHtml($email)));
				} else {
					$this->_getSession()->addError($this->__('This account already exists.'));	
				}
				
				$this->_redirect('*/*/requestaccess');
				return;
			} else {
				$this->_getSession()->addError($this->__('Invalid email address.'));
			}
		} elseif (!empty($params)) {
			$this->_getSession()->addError(Mage::helper('adminhtml')->__('The email address is empty.'));
		}
		$this->loadLayout();
		$this->renderLayout();
	}
	
	    /**
     * Check if user has permissions to access this controller
     *
     * @return boolean
     */
    protected function _isAllowed(){
        return true;
    }
	
	
	
}




