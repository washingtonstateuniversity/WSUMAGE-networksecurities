<?php

class Wsu_Networksecurities_Controller_Sso_Abstract extends Mage_Core_Controller_Front_Action {




	
	/*
	 * This is a general oAuth set up here, but some providers will need to over write this
	 */
	public function makeCustomerData($user_info) {
		$data = array();
		$frist_name = isset($user_info['namePerson/first'])?$user_info['namePerson/first']:"";
		$last_name = isset($user_info['namePerson/last'])?$user_info['namePerson/last']:"";
		$email = $user_info['contact/email'];
		
		if(!$frist_name || !$last_name) {
			if(isset($user_info['namePerson/friendly'])) {
				$frist_name = $user_info['namePerson/friendly'] ; 
				$last_name = $user_info['namePerson/friendly'];
				$data['username']=$user_info['namePerson/friendly'];
			}else{ $emailpart = explode("@", $email);
				$frist_name = $emailpart['0'];
				$last_name  = $emailpart['0'];
				$data['username']=$email;
			}                   
		}
		
		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;
		
		return $data;
	}
	
	public function handleCustomer($user_info){
		$cID=0;
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			 $customerData = Mage::getSingleton('customer/session')->getCustomer();
			 $cID = $customerData->getId();
		}//note we would want to take the passed account id and match it but for now we are just going to use what is set already

		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$data = $this->makeCustomerData($user_info);
		
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();//add
		$website_id = Mage::app()->getStore()->getWebsiteId();//add

		if($cID>0){
			$customer = Mage::getModel('customer/customer')->load($cID);
		}else{
			$customer = $customerHelper->getCustomerByEmail($data['email'], $website_id);
			if(Mage::getStoreConfigFlag('wsu_networksecurities/general_customer/enabled') && (!$customer || !$customer->getId())){
				if($data['username']){
					$customer = Mage::getModel('customer/customer')->loadByUsername($data['username']);
				}
				if(!$customer || !$customer->getId()){
					$customer = Mage::getModel('customer/customer')->loadByUsername($data['email']);
				}
			}
			if(!$customer || !$customer->getId()){
				$customer = $customerHelper->getCustomerByAltSSo($data);
			}
		}

		if(!$customer || !$customer->getId()) {//die("didn't find user");
			if(!$customer || !$customer->getId()) {//die('should not have made this');
				$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
			}
			if(isset($data['authorId'])){
				Mage::getModel('wsu_networksecurities/sso_authorlogin')->addCustomer($data['authorId']);
			}
		}else{
			if($customer->hasSsoMap($customer,$data)==false) {
				$customer = $customer->addSsoMap($customer,$data);
			}
		}
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());	
	}
	
	
	
}