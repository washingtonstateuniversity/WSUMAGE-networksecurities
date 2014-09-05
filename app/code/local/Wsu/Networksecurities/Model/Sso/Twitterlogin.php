<?php
class Wsu_Networksecurities_Model_Sso_Twitterlogin extends Zend_Oauth_Consumer {
	var $_providerName = 'twitter';
	public function getConsumerKey() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/twitter_login/consumer_key'));
	}
	public function getConsumerSecret() {
		return trim(Mage::getStoreConfig('wsu_networksecurities/twitter_login/consumer_secret'));
	}
	/*public function getTwConnectingNotice() {
		return Mage::getStoreConfig('wsu_networksecurities/twitterlogin/connecting_notice');
	}*/


	protected $_options = null;
	public function __construct() {
		$this->_config = new Zend_Oauth_Config;		
		$this->_options = array(
			'consumerKey'       => $this->getConsumerKey(),
			'consumerSecret'    => $this->getConsumerSecret(),
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
	public function getLaunchUrl($account=null) {
		$queries = array();
		if(isset($account)){
			$queries['account']=$account;
		}
		return Mage::getUrl("sociallogin/twitterlogin/login",$queries);
	}
	
}
  
