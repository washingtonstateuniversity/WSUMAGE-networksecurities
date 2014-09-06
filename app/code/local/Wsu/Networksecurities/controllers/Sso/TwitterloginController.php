<?php
class Wsu_Networksecurities_Sso_TwitterloginController extends Wsu_Networksecurities_Controller_Sso_Abstract{
	// url to login
	
    public function loginAction() {
		if (!$this->getAuthorizedToken()) {
			$token = $this->getAuthorization();
		}else{
			$token = $this->getAuthorizedToken();
		}
        return $token;
    }
	
	//url after authorize
	public function userAction() {
		$customerHelper = Mage::helper('wsu_networksecurities/customer');
		$otwitter = Mage::getModel('wsu_networksecurities/sso_twitterlogin');
		$requestToken = Mage::getSingleton('core/session')->getRequestToken();
		
		$oauth_data = array(
			'oauth_token' => $this->getRequest()->getParam('oauth_token'),
			'oauth_verifier' => $this->getRequest()->getParam('oauth_verifier')
         );

		try{
			 $token = $otwitter->getAccessToken($oauth_data, unserialize($requestToken));
		}catch(Exception $e) {
			Mage::getSingleton('core/session')->addError('Login failed as you have not granted access.');			
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}

		if(isset($token)) {
			$this->handleCustomer($token);
		}else{ 
			Mage::getSingleton('core/session')->addError($this->__('Login failed as you have not granted access.'));
			$customerHelper->setJsRedirect(Mage::getBaseUrl());
		}
    }
	
	public function makeCustomerData($user_info) {
		$data = array();

		$name = (string)$user_info->screen_name;		
		$email = $name . '@twitter.com';
		
		$data['provider']="twitter";
		$data['firstname'] = $name;
		$data['lastname'] = $name;			
		$data['email'] = $email;
	
		$data['username'] = $name;
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
        $otwitter = Mage::getModel('wsu_networksecurities/sso_twitterlogin');		
        /* @var $otwitter Twitter_Model_Consumer */
        $otwitter->setCallbackUrl(Mage::getUrl('sociallogin/twitterlogin/user',array('_secure'=>true)));        
        if (!is_null($this->getRequest()->getParam('oauth_token')) && !is_null($this->getRequest()->getParam('oauth_verifier'))) {
            $oauth_data = array(
                'oauth_token' => $this->_getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->_getRequest()->getParam('oauth_verifier')
            );
            $token = $otwitter->getAccessToken($oauth_data, unserialize(Mage::getSingleton('core/session')->getRequestToken()));
            Mage::getSingleton('core/session')->setAccessToken(serialize($token));
            $otwitter->redirect();
        }else{
			$token = $otwitter->getRequestToken();
            Mage::getSingleton('core/session')->setRequestToken(serialize($token));
            $otwitter->redirect();
        }
        return $token;
    }
}