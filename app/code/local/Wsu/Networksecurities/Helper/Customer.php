<?php
class Wsu_Networksecurities_Helper_Customer extends Mage_Core_Helper_Abstract {
	
	public function getCustomerByEmail($email, $website_id=null){ //edit
		$collection = Mage::getModel('customer/customer')->getCollection()
					->addFieldToFilter('email', $email);
		if (Mage::getStoreConfig('customer/account_share/scope') && $website_id!=null) {
			$collection->addFieldToFilter('website_id',$website_id);
		}
		return $collection->getFirstItem();
	}
	
	
	
	
	
}
