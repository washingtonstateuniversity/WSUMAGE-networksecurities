<?php
class Wsu_Networksecurities_Sso_GoogleloginController extends Wsu_Networksecurities_Controller_Sso_Abstract {
	
	public function loginAction() {		
		
		if (!$this->getAuthorizedToken()) {
			$token = $this->getAuthorization();
		}else{ 
			$token = $this->getAuthorizedToken();
		}
		
        return $token;
    }
	
	public function userAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$googlelogin = Mage::getModel('wsu_networksecurities/sso_googlelogin')->getProvider();
		$oauth2 = new Google_Oauth2Service($googlelogin);
		$code = $this->getRequest()->getParam('code');
		if(!$code) {
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');
			$customerHelper->setJsRedirect(Mage::getBaseUrl());	
		}
		$accessToken = $googlelogin->authenticate($code);						
		$user_info = $oauth2->userinfo->get();
		
		$user_info['provider']="google";
		$this->handleCustomer($user_info);
    }
	
	public function makeCustomerData($user_info) {
		$data = array();
		
		$email = $user_info['email'];		
		$name = $user_info['name'];
		$arrName = explode(' ', $name, 2);
		$firstname = $arrName[0];
		$lastname = $arrName[1];			
		$user['email'] = $email;

		$data['provider']=$user_info['provider'];
		$data['email']= $email;
		$data['firstname']=$firstname;
		$data['lastname']=$lastname;

		return $data;
	}

	// if exit access token
	public function getAuthorizedToken() {
        $token = false;
        if (!is_null(Mage::getSingleton('core/session')->getAccessToken())) {
            $token = unserialize(Mage::getSingleton('core/session')->getAccessToken());
        }
        return $token;
    }
     
	// if not exit access token
     public function getAuthorization() {      
       	$scope = array(
					'https://www.googleapis.com/auth/userinfo.profile',
					'https://www.googleapis.com/auth/userinfo.email'
				 );		
		$google = Mage::getModel('wsu_networksecurities/sso_googlelogin')->getProvider();
		$google->setScopes($scope); 	
		$google->setApplicationName(Mage::app()->getStore()->getName()." sign in");	
var_dump($google);

		die();
		$google->authenticate();					
		$authUrl = $google->createAuthUrl();
			var_dump($google);
			var_dump($authUrl);
		die();
		header('Localtion: '.$authUrl);
		die(0);
    }
}