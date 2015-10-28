<?php
class Wsu_Networksecurities_Model_Sso_Facebooklogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	
	var $_providerName = 'facebook';
	
	public function getAppId() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/facebook_login/app_id'));
	}
	public function getAppSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/facebook_login/app_secret'));
	}	
	public function getAuthUrl() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return Mage::getUrl('sociallogin/facebooklogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}
	
	public function createProvider() {
		try{
			require_once(Mage::getBaseDir('lib').DS.'Facebook'.DS.'facebook.php');
		}catch(Exception $e) {}
		
		$facebook = new Facebook(array(
			'appId'  => $this->getAppId(),
			'secret' => $this->getAppSecret(),
			'cookie' => true,
		));
		return $facebook;
	}


	public function getUser() {
		$facebook = $this->getProvider();
    	$userId = $facebook->getUser();
		
		$fbme = NULL;
		if ($userId) {
			try {
				$fbme = $facebook->api('/me');
			} catch (FacebookApiException $e) {
				Mage::getSingleton('core/session')->addError($this->__('ERR:'.$e->getMessage()));
			}
		}
		return $fbme;	
	}
	
	public function getLoginUrl() {
		$facebook = $this->getProvider();
		$loginUrl = $facebook->getLoginUrl(
			array(
				'display'   => 'popup',
				'redirect_uri' => $this->getAuthUrl(),
				'scope' => 'public_profile,email,user_likes,user_friends',
			)
  		);
		var_dump($loginUrl);
			die();
		return $loginUrl;
	}
}
  
