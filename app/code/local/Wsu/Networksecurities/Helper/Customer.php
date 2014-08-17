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
	
	public function createCustomer($data){
		$customer = Mage::getModel('customer/customer');
		$customer->setFirstname($data['firstname']);
		$customer->setLastname($data['lastname']);
		$customer->setEmail($data['email']);
						
		$newPassword = $customer->generatePassword();
		$customer->setPassword($newPassword);
		try{
			$customer->save();
		}catch(Exception $e){}
        		
		return $customer;
	}	
	//create customer login multisite
	public function createCustomerMultiWebsite($data, $website_id, $store_id){
		$customer = Mage::getModel('customer/customer')->setId(null);
		
		$customer->setFirstname($data['firstname']);
		$customer->setLastname($data['lastname']);
		$customer->setEmail($data['email']);
		$customer->setWebsiteId($website_id);
		$customer->setStoreId($store_id);
		
		$newPassword = $customer->generatePassword();
		$customer->setPassword($newPassword);
		try{
			$customer->save();
		}catch(Exception $e){}
		return $customer;
	}	
	
	
}
