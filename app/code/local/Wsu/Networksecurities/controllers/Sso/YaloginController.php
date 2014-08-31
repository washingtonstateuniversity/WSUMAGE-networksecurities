<?php

class Wsu_Networksecurities_Sso_YaloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
	// url to login
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$yalogin = Mage::getModel('wsu_networksecurities/sso_yalogin');
		$hasSession = $yalogin->hasSession();
		if($hasSession == FALSE) {
			$authUrl = $yalogin->getAuthUrl();			
			$this->_redirectUrl($authUrl);
		}else{
			$session = $yalogin->getSession();
			$userSession = $session->getSessionedUser();
			$user_info = $userSession->loadProfile();
			
			if(count($user_info)) {
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
			}else{ 
				$coreSession->addError($this->__('Login failed as you have not granted access.'));
				$customerHelper->setJsRedirect(Mage::getBaseUrl());
			}
		}
		
    }
	
	public function makeCustomerData($user_info) {
		$data = array();

		$emails = $user_info->emails;
		if(isset($emails)){
			foreach($emails as $email) {
				if($email->primary == 1){
					$data['email'] = $email->handle;
				}
			}
		}
		
		$data['username'] = $user_info->nickname;
		if(!isset($user['email'])){
			$data['email']=$data['username'].'@yahoo.com';
		}
		
		$data['firstname'] = $user_info->givenName;
		$data['lastname'] = $user_info->familyName;
		if(!isset($data['firstname'])){
			$data['firstname']=$data['username'];
		}
		if(!isset($user['lastname'])){
			$data['lastname']='';
		}
		$gender = $user_info->gender;
		
		if(isset($gender)){
			$data['gender'] = $gender=="M" ? '1' : '2';
		}
		$birthYear = $user_info->birthYear;
		if(isset($birthYear)){
			$data['dob'] = '1/1/'.$birthYear;
		}
		
		$data['provider']="yahoo";
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;

		return $data;
	}
	
	
	
	
}