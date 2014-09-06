<?php
class Wsu_Networksecurities_Model_Sso_Googlelogin extends Wsu_Networksecurities_Model_Sso_Abstract {
	var $_providerName = 'google';	
	
	public function getConsumerKey() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/google_login/consumer_key'));
	}
	public function getConsumerSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/google_login/consumer_secret'));
	}
	public function getRedirectUri() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return Mage::getUrl('sociallogin/googlelogin/user',array('_secure'=>$isSecure));
	}

	public function createProvider() {

		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'service'.DS.'Google_ServiceResource.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'service'.DS.'Google_Service.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'service'.DS.'Google_Model.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'contrib'.DS.'Google_Oauth2Service.php');
		require_once(Mage::getBaseDir('lib').DS.'Oauth2'.DS.'Google_Client.php');

		$google = new Google_Client;
		var_dump($google);die();
		$google->setClientId($this->getConsumerKey())
				->setClientSecret($this->getConsumerSecret())
				->setRedirectUri($this->getRedirectUri());
		return $google;
	}
}
  
