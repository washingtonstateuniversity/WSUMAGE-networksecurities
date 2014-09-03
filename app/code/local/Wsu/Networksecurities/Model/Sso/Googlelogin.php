<?php



class Wsu_Networksecurities_Model_Sso_Googlelogin extends Mage_Core_Model_Abstract {
	protected $_options = null;
	protected $_config = null;
	var $_provider;
	
	public function __construct() {

	}
	
	public function newProvider() {
		try{
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_ServiceResource.php';
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_Service.php';
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_Model.php';
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'contrib'.DS.'Google_Oauth2Service.php';
			require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'Google_Client.php';
		}catch(Exception $e) {}
		
		$google = new Google_Client;
		$google->setClientId($this->getConsumerKey())
				->setClientSecret($this->getConsumerSecret())
				->setRedirectUri($this->getRedirectUri());
		return $google;
	}

	public function getProvider() {
		if(!isset($this->_provider)){
			$this->_provider = $this->newProvider();
		}
		return $this->_provider;
	}

	
	
	
	public function getConsumerKey() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/googlelogin/consumer_key'));
	}
	public function getConsumerSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/googlelogin/consumer_secret'));
	}
	public function getRedirectUri() {
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return Mage::getUrl('sociallogin/googlelogin/user',array('_secure'=>$isSecure));
	}
}
  
