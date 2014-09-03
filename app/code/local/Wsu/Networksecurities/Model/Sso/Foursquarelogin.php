<?php
class Wsu_Networksecurities_Model_Sso_Foursquarelogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	
	public function getAppkey() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/fqlogin/consumer_key'));
	}
	public function getAppSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/fqlogin/consumer_secret'));
	}
	public function getAuthUrl() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return Mage::getUrl('sociallogin/fqlogin/login', array('_secure'=>$isSecure, 'auth'=>1));
	}
	
	public function createProvider() {
		try{
			require_once(Mage::getBaseDir('lib').DS.'Foursquare'.DS.'FoursquareAPI.class.php');
		}catch(Exception $e) {}
		
		$foursquare = new FoursquareApi(
			$this->getAppkey(),
			$this->getAppSecret(),
            urlencode(Mage::helper('wsu_networksecurities/customer')->getAuthUrl())
		);
		return $foursquare;
	}
	public function getLoginUrl() {
		$foursquare = $this->getProvider();
		$loginUrl = $foursquare->AuthenticationLink();
		return $loginUrl;
	}
}
  
