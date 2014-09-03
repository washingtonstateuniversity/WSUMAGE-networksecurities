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
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$data = $this->makeCustomerData($user_info);
		//get website_id and sote_id of each stores
		$store_id = Mage::app()->getStore()->getStoreId();//add
		$website_id = Mage::app()->getStore()->getWebsiteId();//add

		$customer = $customerHelper->getCustomerByEmail($data['email'], $website_id);
		
		if(!$customer || !$customer->getId()) {
			$customer = $customer->getCustomerAltSSo($customer,$data);
			if(!$customer || !$customer->getId()) {
				$customer = $customerHelper->createCustomerMultiWebsite($data, $website_id, $store_id );
			}
		}else{
			$_customer = $customer->getCustomerAltSSo($customer,$data);
			if(!$_customer || !$_customer->getId()) {
				$customer = $customer->addSsoMap($customer,$data);
			}
		}
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		$customerHelper->setJsRedirect($customerHelper->_loginPostRedirect());	
	}
	
	
	
}