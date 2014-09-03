<?php

class Wsu_Networksecurities_Sso_YahoologinController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
	// url to login
    public function loginAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$yahoologin = Mage::getModel('wsu_networksecurities/sso_yahoologin');
		$hasSession = $yahoologin->hasSession();
		if($hasSession == FALSE) {
			$authUrl = $yahoologin->getAuthUrl();			
			$this->_redirectUrl($authUrl);
		}else{
			$session = $yahoologin->getSession();
			$userSession = $session->getSessionedUser();
			$user_info = $userSession->loadProfile();
			
			if(count($user_info)) {
				$user_info['provider']="yahoo";
				$this->handleCustomer($user_info);
			}else{ 
				Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
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
		
		$data['provider']=$user_info['provider'];
		$data['email']=$email;
		$data['firstname']=$frist_name;
		$data['lastname']=$last_name;

		return $data;
	}
	
	
	
	
}