<?php
class Wsu_Networksecurities_Model_Sso_Facebooklogin extends Mage_Core_Model_Abstract {
	public function newFacebook() {
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Facebook'.DS.'facebook.php';
		}catch(Exception $e) {}
		
		$facebook = new Facebook(array(
			'appId'  => Mage::helper('wsu_networksecurities/customer')->getFbAppId(),
			'secret' => Mage::helper('wsu_networksecurities/customer')->getFbAppSecret(),
			'cookie' => true,
		));
		return $facebook;
	}
	
	public function getUser() {
		$facebook = $this->newFacebook();
    	$userId = $facebook->getUser();
		$fbme = NULL;

		if ($userId) {
			try {
				$fbme = $facebook->api('/me');
			} catch (FacebookApiException $e) {}
		}
		
		return $fbme;	
	}
	
	public function getLoginUrl() {
		$facebook = $this->newFacebook();
		$loginUrl = $facebook->getLoginUrl(
			array(
				'display'   => 'popup',
				'redirect_uri' => Mage::helper('wsu_networksecurities/customer')->getAuthUrl(),
				'scope' => 'email',
			)
  		);
		return $loginUrl;
	}
}
  
