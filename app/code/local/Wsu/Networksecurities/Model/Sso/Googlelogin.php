<?php
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_ServiceResource.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_Service.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'service'.DS.'Google_Model.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'contrib'.DS.'Google_Oauth2Service.php';
require_once Mage::getBaseDir('base').DS.'lib'.DS.'Oauth2'.DS.'Google_Client.php';

class Wsu_Networksecurities_Model_Sso_Googlelogin extends Google_Client {
	protected $_options = null;
	public function __construct() {
		$this->_config = new Google_Client;					
		$this->_config->setClientId(Mage::helper('wsu_networksecurities/customer')->getGoConsumerKey());
		$this->_config->setClientSecret(Mage::helper('wsu_networksecurities/customer')->getGoConsumerSecret());
		$this->_config->setRedirectUri(Mage::getUrl('sociallogin/googlelogin/user',array('_secure'=>true)));		
	}
	public function getUser(){}
	public function getLoginUrl($name="") {}
    public function setIdlogin($openid) {}
}
  
