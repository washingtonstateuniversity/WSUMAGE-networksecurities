<?php
class Wsu_Networksecurities_Model_Sso_Twitterlogin extends Zend_Oauth_Consumer {

	protected $_options = null;
	public function __construct() {
		$this->_config = new Zend_Oauth_Config;		
		$this->_options = array(
			'consumerKey'       => Mage::helper('wsu_networksecurities/customer')->getTwConsumerKey(),
			'consumerSecret'    => Mage::helper('wsu_networksecurities/customer')->getTwConsumerSecret(),
			//'siteUrl'           => 'http://localhost/oss/magento14_3/index.php',
			'signatureMethod'   => 'HMAC-SHA1',
			'version'           => '1.1',
			'requestTokenUrl'   => 'https://api.twitter.com/oauth/request_token',
			'accessTokenUrl'    => 'https://api.twitter.com/oauth/access_token',
			'authorizeUrl'      => 'https://api.twitter.com/oauth/authorize'
		);
	
		$this->_config->setOptions($this->_options);
	}
	
	public function setCallbackUrl($url) {
		$this->_config->setCallbackUrl($url);
	}
}
  
