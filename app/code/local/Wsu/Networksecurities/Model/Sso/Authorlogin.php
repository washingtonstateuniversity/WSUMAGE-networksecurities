<?php
class Wsu_Networksecurities_Model_Sso_Authorlogin extends Mage_Core_Model_Abstract {
	
    public function _construct() {    
        // Note that the membership_id refers to the key field in your database table.
        $this->_init('wsu_networksecurities/authorlogin', 'author_customer_id');
    }
	
	public function addCustomer($authorId = null) {
		$customer = Mage::getModel('customer/customer')->getCollection()
				->getLastItem();
		$customer_id = $customer->getId();
		$model = Mage::getModel('wsu_networksecurities/sso_authorlogin');
		$model	->setData('author_id',$authorId)
				->setData('customer_id',$customer_id)
				->save();
		return true;
	}
	public function checkCustomer($authorId) {
		$model = Mage::getModel('wsu_networksecurities/sso_authorlogin')->getCollection()
						->addFieldToFilter('author_id',$authorId)
						->getLastItem();
		//Zend_Debug::dump($model->getData('customer_id'));die();
		return $model->getData('customer_id');
	}
}